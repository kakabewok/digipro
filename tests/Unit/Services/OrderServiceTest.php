<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\Stock;
use App\Services\InvoiceService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\StockService;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    private OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->orderService = app(OrderService::class);
    }

    public function test_creates_order_with_status_pending_via_qris()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $this->createStock($product, 1);

        $mockPaymentService = Mockery::mock(PaymentService::class);
        $mockPaymentService->shouldReceive('generateQris')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'QRIS123',
                'checkout_url' => 'https://qris.example.com',
                'qr_url' => 'https://qris.example.com/qr',
                'expiry_time' => now()->addMinutes(15)->format('Y-m-d H:i:s')
            ]);
            
        $this->app->instance(PaymentService::class, $mockPaymentService);
        $orderService = app(OrderService::class);

        $result = $orderService->createWithQris($user, $product, 1);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['order']);
        $this->assertEquals('pending', $result['order']->status);
        $this->assertEquals('pending', $result['order']->payment_status);
        $this->assertEquals('qris', $result['order']->payment_method);
        
        $this->assertDatabaseHas('orders', [
            'id' => $result['order']->id,
            'status' => 'pending'
        ]);
    }

    public function test_sets_correct_price_for_customer_role()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct([
            'price_customer' => 10000,
            'price_reseller' => 8000
        ]);

        $price = $this->orderService->getPriceForUser($product, $user, 1);

        $this->assertSame(10000.0, $price);
    }

    public function test_sets_correct_price_for_reseller_role()
    {
        $user = $this->createReseller();
        $product = $this->createProduct([
            'price_customer' => 10000,
            'price_reseller' => 8000
        ]);

        $price = $this->orderService->getPriceForUser($product, $user, 1);

        $this->assertSame(8000.0, $price);
    }

    public function test_applies_bulk_price_when_quantity_meets_min_bulk_qty()
    {
        $user = $this->createReseller(); // Reseller has both reseller and bulk price permission normally? Let's check roles.
        // Let's explicitly give bulk price permission if needed, but OrderService says:
        // if ($user->can('view bulk price') && $quantity >= $product->min_bulk_qty && $product->price_bulk > 0)
        
        // Let's just create a role with 'view bulk price' or use the role if it exists.
        // I will assign the permission to the user directly for the test.
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view bulk price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $product = $this->createProduct([
            'price_customer' => 10000,
            'price_reseller' => 8000,
            'price_bulk' => 7000,
            'min_bulk_qty' => 10
        ]);

        $price = $this->orderService->getPriceForUser($product, $user, 10);

        $this->assertSame(7000.0, $price);
    }

    public function test_create_order_fails_if_no_available_stock()
    {
        $user = $this->createCustomer();
        $user->update(['balance' => 100000]);
        $product = $this->createProduct();
        // Do not create stock

        $result = $this->orderService->createWithBalance($user, $product, 1);

        $this->assertFalse($result['success']);
        $this->assertEquals('Product is out of stock.', $result['error']);
    }

    public function test_process_order_locks_stock_and_marks_it_as_sold()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $success = $this->orderService->processAfterPayment($order);

        $this->assertTrue($success);
        
        $this->assertDatabaseHas('stocks', [
            'id' => $stock->id,
            'status' => 'sold',
        ]);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
            'payment_status' => 'paid',
            'stock_id' => $stock->id,
        ]);
    }
    
    public function test_create_with_balance_deducts_balance_when_payment_method_is_balance()
    {
        $user = $this->createCustomer();
        $user->update(['balance' => 100000]);
        $product = $this->createProduct(['price_customer' => 10000]);
        $stock = $this->createStock($product, 1);

        $result = $this->orderService->createWithBalance($user, $product, 1);

        $this->assertTrue($result['success']);
        $this->assertEquals('completed', $result['order']->status);
        $this->assertStringStartsWith('INV-', $result['order']->invoice_number);
        
        $user->refresh();
        $this->assertEquals(90000, $user->balance);
        
        $stock->refresh();
        $this->assertEquals('sold', $stock->status);
    }

    public function test_process_order_fails_if_stock_already_sold()
    {
        Log::shouldReceive('error')->once();
        
        $user = $this->createCustomer();
        $product = $this->createProduct();
        // No stock available

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
        ]);

        $success = $this->orderService->processAfterPayment($order);

        $this->assertFalse($success);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
            'notes' => 'Stock unavailable after payment'
        ]);
    }

    public function test_cancel_order_sets_order_status_to_cancelled_and_releases_stock()
    {
        $user = $this->createCustomer();
        $product = $this->createProduct();
        $stock = $this->createStock($product, 1);
        $stock->update(['status' => 'sold']);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'stock_id' => $stock->id,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->orderService->cancelOrder($order);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
            'payment_status' => 'expired'
        ]);
        
        $this->assertDatabaseHas('stocks', [
            'id' => $stock->id,
            'status' => 'available',
        ]);
    }
}
