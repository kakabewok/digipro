<?php

namespace App\Jobs;

use App\Models\Deposit;
use App\Models\Order;
use App\Services\DepositService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Handles incoming payment webhook from AutoGoPay.
 * Determines if payment is for an order or deposit and dispatches accordingly.
 */
class HandlePaymentWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public array $payload,
    ) {}

    public function handle(DepositService $depositService): void
    {
        $transaction = $this->payload['transaction'] ?? [];
        $transactionId = $transaction['id'] ?? null;
        $status = $transaction['status'] ?? null;

        if (! $transactionId || $status !== 'settlement') {
            Log::info('Webhook ignored: not a settlement', ['payload' => $this->payload]);
            return;
        }

        // Check if this is an order payment
        $order = Order::where('qris_reference', $transactionId)
            ->where('payment_status', 'pending')
            ->first();

        if ($order) {
            Log::info('Webhook: processing order payment', ['order_id' => $order->id]);
            ProcessOrderJob::dispatch($order);
            return;
        }

        // Check if this is a deposit payment
        $deposit = Deposit::where('qris_reference', $transactionId)
            ->where('payment_status', 'pending')
            ->first();

        if ($deposit) {
            Log::info('Webhook: processing deposit payment', ['deposit_id' => $deposit->id]);
            HandleDepositWebhookJob::dispatch($deposit);
            return;
        }

        Log::warning('Webhook: no matching order or deposit', [
            'transaction_id' => $transactionId,
        ]);
    }
}
