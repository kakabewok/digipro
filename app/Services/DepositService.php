<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handles deposit (balance top-up) operations.
 */
class DepositService
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    /**
     * Create a deposit request and generate QRIS.
     *
     * @return array{success: bool, deposit: Deposit|null, qr_url: string|null, checkout_url: string|null, error: string|null}
     */
    public function create(User $user, int $amount): array
    {
        // Generate QRIS via AutoGoPay
        $qris = $this->paymentService->generateQris($amount);

        if (! $qris['success']) {
            return [
                'success' => false,
                'deposit' => null,
                'qr_url' => null,
                'checkout_url' => null,
                'error' => $qris['error'],
            ];
        }

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_status' => 'pending',
            'qris_reference' => $qris['transaction_id'],
            'qris_checkout_url' => $qris['checkout_url'],
            'qris_expired_at' => $qris['expiry_time'],
        ]);

        return [
            'success' => true,
            'deposit' => $deposit,
            'qr_url' => $qris['qr_url'],
            'checkout_url' => $qris['checkout_url'],
            'error' => null,
        ];
    }

    /**
     * Process deposit after payment confirmation (from webhook).
     * Credits user balance.
     */
    public function processPayment(Deposit $deposit): bool
    {
        return DB::transaction(function () use ($deposit) {
            $lockedDeposit = Deposit::lockForUpdate()->find($deposit->id);

            // Idempotency: skip if already processed
            if ($lockedDeposit->payment_status !== 'pending') {
                Log::info('Deposit already processed', ['deposit_id' => $deposit->id]);
                return true;
            }

            // Mark deposit as paid
            $lockedDeposit->update(['payment_status' => 'paid']);

            // Credit user balance
            $user = User::lockForUpdate()->find($lockedDeposit->user_id);
            $user->increment('balance', $lockedDeposit->amount);

            return true;
        });
    }

    /**
     * Mark expired deposits.
     */
    public function expireDeposit(Deposit $deposit): void
    {
        $deposit->update(['payment_status' => 'expired']);
    }
}
