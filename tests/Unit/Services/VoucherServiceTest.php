<?php

namespace Tests\Unit\Services;

use App\Services\VoucherService;
use Tests\TestCase;

class VoucherServiceTest extends TestCase
{
    private VoucherService $voucherService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->voucherService = app(VoucherService::class);
    }

    public function test_validate_returns_voucher_if_code_is_valid()
    {
        $voucher = $this->createVoucher(['code' => 'VALID10']);
        
        $result = $this->voucherService->validate('VALID10', 100000);
        
        $this->assertTrue($result['valid']);
        $this->assertEquals($voucher->id, $result['voucher']->id);
    }

    public function test_validate_throws_error_if_code_not_found()
    {
        $result = $this->voucherService->validate('INVALID', 100000);
        
        $this->assertFalse($result['valid']);
        $this->assertEquals('Voucher not found.', $result['error']);
    }

    public function test_validate_throws_error_if_voucher_is_expired()
    {
        $this->createVoucher(['code' => 'EXPIRED', 'expired_at' => now()->subDay()]);
        
        $result = $this->voucherService->validate('EXPIRED', 100000);
        
        $this->assertFalse($result['valid']);
        $this->assertEquals('Voucher has expired.', $result['error']);
    }

    public function test_validate_throws_error_if_max_usage_is_reached()
    {
        $this->createVoucher(['code' => 'MAXUSAGE', 'max_usage' => 5, 'used_count' => 5]);
        
        $result = $this->voucherService->validate('MAXUSAGE', 100000);
        
        $this->assertFalse($result['valid']);
        $this->assertEquals('Voucher usage limit reached.', $result['error']);
    }

    public function test_validate_throws_error_if_purchase_amount_is_below_min_purchase()
    {
        $this->createVoucher(['code' => 'MINPURCHASE', 'min_purchase' => 50000]);
        
        $result = $this->voucherService->validate('MINPURCHASE', 40000);
        
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Minimum purchase is', $result['error']);
    }

    public function test_accepts_voucher_with_min_purchase_of_0()
    {
        $this->createVoucher(['code' => 'ZERO', 'min_purchase' => 0]);
        
        $result = $this->voucherService->validate('ZERO', 10000);
        
        $this->assertTrue($result['valid']);
    }

    public function test_apply_correctly_calculates_nominal_discount()
    {
        $voucher = $this->createVoucher(['type' => 'nominal', 'value' => 15000]);
        
        $discount = $this->voucherService->calculateDiscount($voucher, 100000);
        
        $this->assertSame(15000.0, $discount);
    }

    public function test_apply_correctly_calculates_percentage_discount()
    {
        $voucher = $this->createVoucher(['type' => 'percentage', 'value' => 10]); // 10%
        
        $discount = $this->voucherService->calculateDiscount($voucher, 100000);
        
        $this->assertSame(10000.0, $discount);
    }

    public function test_discount_does_not_exceed_total_price()
    {
        $voucher = $this->createVoucher(['type' => 'nominal', 'value' => 100000]);
        
        $discount = $this->voucherService->calculateDiscount($voucher, 50000);
        
        $this->assertSame(50000.0, $discount);
    }

    public function test_increments_used_count_after_apply()
    {
        $voucher = $this->createVoucher(['used_count' => 2]);
        
        $this->voucherService->incrementUsage($voucher);
        
        $voucher->refresh();
        $this->assertEquals(3, $voucher->used_count);
    }
}
