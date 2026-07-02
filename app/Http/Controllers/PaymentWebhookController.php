<?php

namespace App\Http\Controllers;

use App\Jobs\HandlePaymentWebhookJob;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles incoming payment webhooks from AutoGoPay.
 * Verifies signature before dispatching job.
 */
class PaymentWebhookController extends Controller
{
    public function handle(Request $request, PaymentService $paymentService): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Signature', '');

        // Verify webhook signature (HMAC-SHA256)
        if (! $paymentService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Webhook: invalid signature', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['success' => false, 'message' => 'Invalid signature'], 401);
        }

        $data = $request->all();

        Log::info('Webhook received', [
            'event' => $data['event'] ?? 'unknown',
            'transaction_id' => $data['transaction']['id'] ?? 'unknown',
        ]);

        // Dispatch job to handle the payment asynchronously
        // Must respond within 10 seconds per AutoGoPay docs
        HandlePaymentWebhookJob::dispatch($data);

        return response()->json(['success' => true]);
    }
}
