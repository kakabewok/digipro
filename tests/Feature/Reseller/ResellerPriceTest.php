<?php

namespace Tests\Feature\Reseller;

use App\Models\Order;
use Tests\TestCase;

class ResellerPriceTest extends TestCase
{
    public function test_reseller_sees_price_reseller_on_product_list()
    {
        $user = $this->createReseller();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $this->createProduct(['name' => 'Test Product', 'price_customer' => 50000, 'price_reseller' => 40000]);

        $response = $this->actingAs($user)->get('/products');

        $response->assertSee('40.000');
    }

    public function test_reseller_sees_price_reseller_on_product_detail()
    {
        $user = $this->createReseller();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $product = $this->createProduct(['price_customer' => 50000, 'price_reseller' => 40000]);

        $response = $this->actingAs($user)->get('/products/' . $product->slug);

        $response->assertSee('40.000');
    }

    public function test_reseller_sees_price_reseller_on_checkout()
    {
        $user = $this->createReseller();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $product = $this->createProduct(['price_customer' => 50000, 'price_reseller' => 40000, 'status' => 'active']);
        $this->createStock($product, 1);

        $response = $this->actingAs($user)->get('/checkout/' . $product->slug);

        $response->assertSee('40.000');
    }

    public function test_reseller_sees_price_bulk_when_quantity_meets_min_bulk_qty()
    {
        $user = $this->createReseller();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view bulk price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $product = $this->createProduct([
            'price_customer' => 50000, 
            'price_reseller' => 40000, 
            'price_bulk' => 35000,
            'min_bulk_qty' => 10,
            'status' => 'active'
        ]);
        $this->createStock($product, 10);

        // Usually checkout updates price via JS, but if backend returns it:
        // We test checkout form submission or an API endpoint that gets the price
        // Let's test the order creation with that quantity directly to ensure correct price is saved
        $user->update(['balance' => 400000]);

        $response = $this->actingAs($user)->post('/checkout', [
            'product_id' => $product->id,
            'quantity' => 10,
            'payment_method' => 'balance',
        ]);

        $response->assertRedirect();
        
        // 35000 * 10 = 350000
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 35000, // Important check
            'total' => 350000,
        ]);
    }

    public function test_customer_does_not_see_price_reseller()
    {
        $user = $this->createCustomer();
        
        $this->createProduct(['name' => 'Test Product', 'price_customer' => 50000, 'price_reseller' => 40000]);

        $response = $this->actingAs($user)->get('/products');

        $response->assertSee('50.000');
        $response->assertDontSee('40.000');
    }

    public function test_order_is_saved_with_price_reseller_for_reseller_user()
    {
        $user = $this->createReseller();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $user->update(['balance' => 100000]);
        
        $product = $this->createProduct(['price_customer' => 50000, 'price_reseller' => 40000, 'status' => 'active']);
        $this->createStock($product, 1);

        $response = $this->actingAs($user)->post('/checkout', [
            'product_id' => $product->id,
            'quantity' => 1,
            'payment_method' => 'balance',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'price' => 40000,
            'total' => 40000,
        ]);
    }

    public function test_order_is_saved_with_price_bulk_when_bulk_qty_met()
    {
        $user = $this->createReseller();
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view bulk price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        $user->update(['balance' => 400000]);
        
        $product = $this->createProduct([
            'price_customer' => 50000, 
            'price_reseller' => 40000,
            'price_bulk' => 30000,
            'min_bulk_qty' => 10,
            'status' => 'active'
        ]);
        $this->createStock($product, 10);

        $response = $this->actingAs($user)->post('/checkout', [
            'product_id' => $product->id,
            'quantity' => 10,
            'payment_method' => 'balance',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'price' => 30000,
            'total' => 300000,
        ]);
    }
}
