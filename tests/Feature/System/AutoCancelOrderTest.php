<?php

namespace Tests\Feature\System;

use App\Models\Order;
use App\Models\Stock;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AutoCancelOrderTest extends TestCase
{
    public function test_expired_pending_orders_are_cancelled_by_the_command()
    {
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1, ['status' => 'sold']);
        
        $order = Order::factory()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
            'stock_id' => $stock->id,
            'qris_expired_at' => now()->subMinutes(5) // Expired 5 minutes ago
        ]);

        Artisan::call('orders:cancel-expired');

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals('expired', $order->payment_status);
    }

    public function test_cancelled_order_releases_stock_back_to_available()
    {
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1, ['status' => 'sold']);
        
        $order = Order::factory()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
            'stock_id' => $stock->id,
            'qris_expired_at' => now()->subMinutes(5)
        ]);

        Artisan::call('orders:cancel-expired');

        $stock->refresh();
        $this->assertEquals('available', $stock->status);
    }

    public function test_non_expired_pending_orders_are_not_cancelled()
    {
        $order = Order::factory()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
            'qris_expired_at' => now()->addMinutes(5) // Expires in 5 minutes
        ]);

        Artisan::call('orders:cancel-expired');

        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }

    public function test_paid_orders_are_not_cancelled_by_the_command()
    {
        $order = Order::factory()->create([
            'status' => 'completed',
            'payment_status' => 'paid',
            'qris_expired_at' => now()->subMinutes(5) // Doesn't matter because it's paid
        ]);

        Artisan::call('orders:cancel-expired');

        $order->refresh();
        $this->assertEquals('completed', $order->status);
    }

    public function test_command_output_reports_number_of_cancelled_orders()
    {
        Order::factory()->count(2)->create([
            'status' => 'pending',
            'payment_status' => 'pending',
            'qris_expired_at' => now()->subMinutes(5)
        ]);

        $this->artisan('orders:cancel-expired')
            ->expectsOutputToContain('Cancelled 2 expired orders.')
            ->assertExitCode(0);
    }
}
