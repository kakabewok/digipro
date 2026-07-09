<?php

namespace Tests\Feature\Customer;

use App\Models\Order;
use App\Services\PaymentService;
use Mockery;
use Tests\TestCase;

class PaymentQrisTest extends TestCase
{
    public function test_qris_is_generated_when_payment_method_is_qris()
    {
        $user = $this->createCustomer();
        
        $product = $this->createProduct(['price_customer' => 50000, 'status' => 'active']);
        $stock = $this->createStock($product, 1);

        $mockPaymentService = Mockery::mock(PaymentService::class);
        $mockPaymentService->shouldReceive('generateQris')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'QRIS-12345',
                'checkout_url' => 'https://qris.example.com',
                'qr_url' => 'https://qris.example.com/qr',
                'expiry_time' => now()->addMinutes(15)->format('Y-m-d H:i:s')
            ]);
            
        $this->app->instance(PaymentService::class, $mockPaymentService);

        $response = $this->actingAs($user)->post('/checkout', [
            'product_id' => $product->id,
            'quantity' => 1,
            'payment_method' => 'qris',
        ]);

        $response->assertRedirect(); // likely redirects to order details or payment page
        
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'qris_reference' => 'QRIS-12345',
        ]);
    }

    public function test_order_remains_pending_until_webhook()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct(['price_customer' => 50000, 'status' => 'active']);
        
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'qris_reference' => 'QRIS-12345',
        ]);

        $response = $this->actingAs($user)->get('/orders/' . $order->invoice_number);

        $response->assertStatus(200);
        $response->assertSee('Pending');
    }
}
