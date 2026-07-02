<?php

namespace App\Services;

use App\Models\Voucher;

/**
 * Validates and applies voucher discounts.
 */
class VoucherService
{
    /**
     * Validate a voucher code against a purchase amount.
     *
     * @return array{valid: bool, voucher: Voucher|null, error: string|null}
     */
    public function validate(string $code, float $purchaseAmount): array
    {
        $voucher = Voucher::where('code', $code)->first();

        if (! $voucher) {
            return ['valid' => false, 'voucher' => null, 'error' => 'Voucher not found.'];
        }

        if ($voucher->expired_at && $voucher->expired_at->isPast()) {
            return ['valid' => false, 'voucher' => $voucher, 'error' => 'Voucher has expired.'];
        }

        if ($voucher->max_usage > 0 && $voucher->used_count >= $voucher->max_usage) {
            return ['valid' => false, 'voucher' => $voucher, 'error' => 'Voucher usage limit reached.'];
        }

        if ($purchaseAmount < $voucher->min_purchase) {
            return [
                'valid' => false,
                'voucher' => $voucher,
                'error' => "Minimum purchase is Rp " . number_format($voucher->min_purchase, 0, ',', '.') . ".",
            ];
        }

        return ['valid' => true, 'voucher' => $voucher, 'error' => null];
    }

    /**
     * Calculate discount amount based on voucher type.
     */
    public function calculateDiscount(Voucher $voucher, float $purchaseAmount): float
    {
        if ($voucher->type === 'nominal') {
            // Nominal discount cannot exceed purchase amount
            return min($voucher->value, $purchaseAmount);
        }

        if ($voucher->type === 'percentage') {
            // Percentage of purchase amount
            return round($purchaseAmount * ($voucher->value / 100), 2);
        }

        return 0;
    }

    /**
     * Increment voucher usage count.
     */
    public function incrementUsage(Voucher $voucher): void
    {
        $voucher->increment('used_count');
    }
}
