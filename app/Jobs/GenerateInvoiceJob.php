<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Generates an invoice number for an order if one doesn't exist.
 * Typically not needed since InvoiceService is called during order creation,
 * but available as a fallback/retry mechanism.
 */
class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Order $order,
    ) {}

    public function handle(InvoiceService $invoiceService): void
    {
        if (empty($this->order->invoice_number)) {
            $this->order->update([
                'invoice_number' => $invoiceService->generate(),
            ]);
        }
    }
}
