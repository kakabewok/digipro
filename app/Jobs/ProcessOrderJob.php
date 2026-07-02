<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Processes an order after payment is confirmed.
 * Locks stock, picks available item, marks sold, completes order.
 */
class ProcessOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public Order $order,
    ) {}

    public function handle(OrderService $orderService): void
    {
        Log::info('Processing order', ['order_id' => $this->order->id]);

        $result = $orderService->processAfterPayment($this->order);

        if (! $result) {
            Log::error('Order processing failed', ['order_id' => $this->order->id]);
        }
    }
}
