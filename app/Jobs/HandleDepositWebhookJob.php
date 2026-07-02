<?php

namespace App\Jobs;

use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Processes a deposit after QRIS payment is confirmed.
 * Credits user balance.
 */
class HandleDepositWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public Deposit $deposit,
    ) {}

    public function handle(DepositService $depositService): void
    {
        Log::info('Processing deposit payment', ['deposit_id' => $this->deposit->id]);

        $result = $depositService->processPayment($this->deposit);

        if (! $result) {
            Log::error('Deposit processing failed', ['deposit_id' => $this->deposit->id]);
        }
    }
}
