<?php

namespace Tests\Feature\System;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LockStockTest extends TestCase
{
    public function test_simultaneous_orders_do_not_sell_same_stock_twice()
    {
        // This is tricky to test fully in PHPUnit without actual concurrency,
        // but we can test the lock mechanism via mocking or verifying DB transactions.
        // A common way to test pessimistic locks is by starting a transaction,
        // locking a row, and ensuring it behaves correctly, or just testing the standard flow.
        
        // We will test that when stock is locked by lockAndPick, it is marked as sold immediately
        // so the next call will not get it.
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);
        
        $orderService = app(OrderService::class);
        $user1 = User::factory()->create(['balance' => 100000]);
        $user2 = User::factory()->create(['balance' => 100000]);
        
        $result1 = $orderService->createWithBalance($user1, $product, 1);
        $result2 = $orderService->createWithBalance($user2, $product, 1);
        
        $this->assertTrue($result1['success']);
        $this->assertFalse($result2['success']); // Second one fails because stock is gone
        $this->assertEquals('Product is out of stock.', $result2['error']);
    }

    public function test_stock_is_locked_during_order_processing()
    {
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);
        
        $order = Order::factory()->create([
            'user_id' => User::factory()->create()->id,
            'product_id' => $product->id,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        
        $orderService = app(OrderService::class);
        $success = $orderService->processAfterPayment($order);
        
        $this->assertTrue($success);
        
        $stock->refresh();
        $this->assertEquals('sold', $stock->status);
    }

    public function test_released_stock_becomes_available_for_next_order()
    {
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);
        
        $orderService = app(OrderService::class);
        $user1 = User::factory()->create(['balance' => 100000]);
        $user2 = User::factory()->create(['balance' => 100000]);
        
        $result1 = $orderService->createWithBalance($user1, $product, 1);
        $this->assertTrue($result1['success']);
        
        // Cancel first order to release stock
        $orderService->cancelOrder($result1['order']);
        
        // Second order should now succeed
        $result2 = $orderService->createWithBalance($user2, $product, 1);
        $this->assertTrue($result2['success']);
        
        $this->assertEquals($stock->id, $result2['order']->stock_id);
    }

    public function test_order_fails_gracefully_if_all_stock_is_locked()
    {
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);
        
        // Explicitly lock and sell the stock outside of OrderService
        $stock->update(['status' => 'sold']);
        
        $orderService = app(OrderService::class);
        $user = User::factory()->create(['balance' => 100000]);
        
        $result = $orderService->createWithBalance($user, $product, 1);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Product is out of stock.', $result['error']);
    }
}
