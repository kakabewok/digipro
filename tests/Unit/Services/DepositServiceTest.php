<?php

namespace Tests\Unit\Services;

use App\Models\Deposit;
use App\Services\DepositService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class DepositServiceTest extends TestCase
{
    public function test_create_deposit_creates_deposit_record_with_status_pending()
    {
        $user = $this->createCustomer();
        
        $mockPaymentService = Mockery::mock(PaymentService::class);
        $mockPaymentService->shouldReceive('generateQris')
            ->once()
            ->with(50000)
            ->andReturn([
                'success' => true,
                'transaction_id' => 'QRIS-DEP-123',
                'checkout_url' => 'https://qris.example.com',
                'qr_url' => 'https://qris.example.com/qr',
                'expiry_time' => now()->addMinutes(15)->format('Y-m-d H:i:s')
            ]);
            
        $this->app->instance(PaymentService::class, $mockPaymentService);
        $depositService = app(DepositService::class);

        $result = $depositService->create($user, 50000);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['deposit']);
        $this->assertEquals('pending', $result['deposit']->payment_status);
        $this->assertEquals('QRIS-DEP-123', $result['deposit']->qris_reference);
        $this->assertNotNull($result['deposit']->qris_expired_at);
        
        $this->assertDatabaseHas('deposits', [
            'id' => $result['deposit']->id,
            'amount' => 50000,
            'payment_status' => 'pending'
        ]);
    }

    public function test_handle_success_updates_deposit_status_to_paid_and_adds_balance()
    {
        $user = $this->createCustomer();
        $user->update(['balance' => 10000]);
        
        $deposit = Deposit::factory()->create([
            'user_id' => $user->id,
            'amount' => 50000,
            'payment_status' => 'pending',
            'qris_reference' => 'QRIS-DEP-123'
        ]);

        $depositService = app(DepositService::class);
        $success = $depositService->processPayment($deposit);

        $this->assertTrue($success);
        
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'payment_status' => 'paid'
        ]);
        
        $user->refresh();
        $this->assertEquals(60000, $user->balance);
    }
    
    public function test_process_payment_is_idempotent()
    {
        Log::shouldReceive('info')->once()->withArgs(function($message) {
            return $message === 'Deposit already processed';
        });
        
        $user = $this->createCustomer();
        $user->update(['balance' => 60000]);
        
        $deposit = Deposit::factory()->create([
            'user_id' => $user->id,
            'amount' => 50000,
            'payment_status' => 'paid',
            'qris_reference' => 'QRIS-DEP-123'
        ]);

        $depositService = app(DepositService::class);
        $success = $depositService->processPayment($deposit);

        $this->assertTrue($success);
        
        $user->refresh();
        $this->assertEquals(60000, $user->balance); // Balance should NOT increase
    }
}
