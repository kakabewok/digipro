<?php

namespace Tests\Feature\Customer;

use App\Models\Deposit;
use App\Services\PaymentService;
use Mockery;
use Tests\TestCase;

class DepositTest extends TestCase
{
    public function test_customer_can_access_deposit_page()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->get('/deposits');

        $response->assertStatus(200);
    }

    public function test_qris_is_generated_for_valid_deposit_amount()
    {
        $user = $this->createCustomer();

        $mockPaymentService = Mockery::mock(PaymentService::class);
        $mockPaymentService->shouldReceive('generateQris')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'QRIS-DEP-12345',
                'checkout_url' => 'https://qris.example.com',
                'qr_url' => 'https://qris.example.com/qr',
                'expiry_time' => now()->addMinutes(15)->format('Y-m-d H:i:s')
            ]);
            
        $this->app->instance(PaymentService::class, $mockPaymentService);

        $response = $this->actingAs($user)->post('/deposits', [
            'amount' => 50000,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('deposits', [
            'user_id' => $user->id,
            'amount' => 50000,
            'payment_status' => 'pending',
            'qris_reference' => 'QRIS-DEP-12345',
        ]);
    }

    public function test_deposit_history_shows_all_deposits_for_current_user()
    {
        $user = $this->createCustomer();
        Deposit::factory()->create(['user_id' => $user->id, 'amount' => 50000]);

        $response = $this->actingAs($user)->get('/deposits');

        $response->assertSee('50.000');
    }

    public function test_deposit_history_does_not_show_other_users_deposits()
    {
        $user1 = $this->createCustomer();
        $user2 = $this->createCustomer();
        
        Deposit::factory()->create(['user_id' => $user2->id, 'amount' => 75000]);

        $response = $this->actingAs($user1)->get('/deposits');

        $response->assertDontSee('75.000');
    }

    public function test_deposit_fails_with_amount_of_0()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->post('/deposits', [
            'amount' => 0,
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_deposit_fails_with_negative_amount()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->post('/deposits', [
            'amount' => -10000,
        ]);

        $response->assertSessionHasErrors('amount');
    }
}
