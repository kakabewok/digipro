<?php

namespace Tests\Feature\Customer;

use Tests\TestCase;

class PaymentBalanceTest extends TestCase
{
    public function test_customer_can_pay_with_sufficient_balance()
    {
        $user = $this->createCustomer();
        $user->update(['balance' => 100000]);
        
        $product = $this->createProduct(['price_customer' => 50000, 'status' => 'active']);
        $stock = $this->createStock($product, 1);

        // Assuming checkout form submits to /checkout
        $response = $this->actingAs($user)->post('/checkout', [
            'product_id' => $product->id,
            'quantity' => 1,
            'payment_method' => 'balance',
        ]);

        $response->assertRedirect();
        
        $user->refresh();
        $this->assertEquals(50000, $user->balance);
        
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);
        
        $stock->refresh();
        $this->assertEquals('sold', $stock->status);
    }

    public function test_payment_fails_if_balance_is_insufficient()
    {
        $user = $this->createCustomer();
        $user->update(['balance' => 10000]); // Insufficient
        
        $product = $this->createProduct(['price_customer' => 50000, 'status' => 'active']);
        $stock = $this->createStock($product, 1);

        $response = $this->actingAs($user)->post('/checkout', [
            'product_id' => $product->id,
            'quantity' => 1,
            'payment_method' => 'balance',
        ]);

        $response->assertSessionHasErrors('error'); // or whichever key contains 'Insufficient balance'
        
        $user->refresh();
        $this->assertEquals(10000, $user->balance);
        
        $this->assertDatabaseMissing('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'completed'
        ]);
    }
}
