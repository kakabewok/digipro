<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

/**
 * Generates unique invoice numbers in format: INV-{YYYYMMDD}-{RANDOM_6_DIGIT}
 */
class InvoiceService
{
    /**
     * Generate a unique invoice number.
     * Retries if a collision occurs (extremely unlikely).
     */
    public function generate(): string
    {
        $maxRetries = 5;

        for ($i = 0; $i < $maxRetries; $i++) {
            $invoice = sprintf(
                'INV-%s-%s',
                now()->format('Ymd'),
                strtoupper(Str::random(6))
            );

            if (! Order::where('invoice_number', $invoice)->exists()) {
                return $invoice;
            }
        }

        // Fallback with timestamp to ensure uniqueness
        return sprintf(
            'INV-%s-%s%s',
            now()->format('Ymd'),
            now()->format('His'),
            strtoupper(Str::random(3))
        );
    }
}
