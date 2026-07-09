<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Services\InvoiceService;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    private InvoiceService $invoiceService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->invoiceService = app(InvoiceService::class);
    }

    public function test_generate_returns_string_starting_with_inv()
    {
        $invoice = $this->invoiceService->generate();
        
        $this->assertStringStartsWith('INV-', $invoice);
    }

    public function test_contains_current_date_in_yyyymmdd_format()
    {
        $invoice = $this->invoiceService->generate();
        $date = now()->format('Ymd');
        
        $this->assertStringContainsString("-$date-", $invoice);
    }

    public function test_ends_with_6_character_alphanumeric_suffix()
    {
        $invoice = $this->invoiceService->generate();
        
        $parts = explode('-', $invoice);
        $suffix = end($parts);
        
        $this->assertEquals(6, strlen($suffix));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{6}$/', $suffix);
    }

    public function test_generates_unique_invoice_numbers_across_multiple_calls()
    {
        $invoice1 = $this->invoiceService->generate();
        $invoice2 = $this->invoiceService->generate();
        
        $this->assertNotEquals($invoice1, $invoice2);
    }
}
