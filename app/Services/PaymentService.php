<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Payment service integrating with AutoGoPay QRIS API.
 * Designed to be provider-agnostic — swap implementation by changing this class.
 *
 * API Docs: https://autogopay.site/docs
 * Base URL: https://v1-gateway.autogopay.site
 *
 * Endpoints used:
 *   POST /qris/generate  — create QRIS payment
 *   POST /qris/status    — check payment status
 *   POST /qris/cancel    — cancel pending QRIS
 *
 * Webhook: POST to our /webhook/payment
 *   Headers: X-Signature (HMAC-SHA256 of body using API key)
 *   Event: transaction.received
 *   Status values: pending, settlement, expire, cancel
 */
class PaymentService
{
    private string $baseUrl;

    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.autogopay.base_url', 'https://v1-gateway.autogopay.site');
        $this->apiKey = config('services.autogopay.api_key', '');
    }

    /**
     * Generate a QRIS payment.
     *
     * @param  int  $amount  Amount in IDR (1 - 10,000,000)
     * @return array{success: bool, transaction_id: string|null, order_id: string|null, qr_url: string|null, checkout_url: string|null, expiry_time: string|null, error: string|null}
     */
    public function generateQris(int $amount): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->baseUrl}/qris/generate", [
                    'amount' => $amount,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return [
                    'success' => true,
                    'transaction_id' => $data['data']['transaction_id'] ?? null,
                    'order_id' => $data['data']['order_id'] ?? null,
                    'qr_url' => $data['data']['qr_url'] ?? null,
                    'checkout_url' => $data['data']['checkout_url'] ?? null,
                    'expiry_time' => $data['data']['expiry_time'] ?? null,
                    'error' => null,
                ];
            }

            Log::error('AutoGoPay QRIS generation failed', [
                'status' => $response->status(),
                'response' => $data,
            ]);

            return [
                'success' => false,
                'transaction_id' => null,
                'order_id' => null,
                'qr_url' => null,
                'checkout_url' => null,
                'expiry_time' => null,
                'error' => $data['message'] ?? 'Failed to generate QRIS',
            ];
        } catch (\Exception $e) {
            Log::error('AutoGoPay QRIS generation exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'transaction_id' => null,
                'order_id' => null,
                'qr_url' => null,
                'checkout_url' => null,
                'expiry_time' => null,
                'error' => 'Payment service unavailable',
            ];
        }
    }

    /**
     * Check QRIS payment status.
     *
     * @return array{success: bool, status: string|null, error: string|null}
     */
    public function checkStatus(string $transactionId): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post("{$this->baseUrl}/qris/status", [
                    'transaction_id' => $transactionId,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return [
                    'success' => true,
                    'status' => $data['data']['transaction_status'] ?? null,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'status' => null,
                'error' => $data['message'] ?? 'Failed to check status',
            ];
        } catch (\Exception $e) {
            Log::error('AutoGoPay status check exception', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status' => null,
                'error' => 'Payment service unavailable',
            ];
        }
    }

    /**
     * Cancel a pending QRIS payment.
     */
    public function cancelQris(string $transactionId): bool
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post("{$this->baseUrl}/qris/cancel", [
                    'transaction_id' => $transactionId,
                ]);

            return $response->successful() && ($response->json('success') ?? false);
        } catch (\Exception $e) {
            Log::error('AutoGoPay cancel exception', [
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Verify webhook signature from AutoGoPay.
     * Signature: HMAC-SHA256 of raw request body, using API key as secret.
     * Header: X-Signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha256', $payload, $this->apiKey);

        return hash_equals($expected, $signature);
    }
}
