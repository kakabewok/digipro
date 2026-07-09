<?php

namespace Tests\Traits;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Voucher;
use Spatie\Permission\Models\Role;

trait InteractsWithRoles
{
    public function createAdmin()
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage products', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $user->assignRole($role);

        return $user;
    }

    public function createReseller()
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'reseller', 'guard_name' => 'web']);
        
        $permission1 = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $permission2 = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view bulk price', 'guard_name' => 'web']);
        $role->givePermissionTo($permission1);
        $role->givePermissionTo($permission2);

        $user->assignRole($role);

        return $user;
    }

    public function createCustomer()
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }

    public function createProduct(array $attributes = [])
    {
        $category = Category::firstOrCreate(
            ['slug' => 'test-category'],
            ['name' => 'Test Category']
        );

        $defaultAttributes = [
            'category_id' => $category->id,
            'name' => 'Test Product ' . uniqid(),
            'slug' => 'test-product-' . uniqid(),
            'description' => 'Test description',
            'price_customer' => 100000,
            'price_reseller' => 80000,
            'price_bulk' => 70000,
            'min_bulk_qty' => 10,
            'status' => 'active',
        ];

        return Product::create(array_merge($defaultAttributes, $attributes));
    }

    public function createStock($product, int $count = 1)
    {
        $stocks = collect();

        for ($i = 0; $i < $count; $i++) {
            $stocks->push(Stock::create([
                'product_id' => $product->id,
                'value' => 'ACCOUNT-'.uniqid(),
                'status' => 'available',
            ]));
        }

        return $stocks->count() === 1 ? $stocks->first() : $stocks;
    }

    public function createVoucher(array $attributes = [])
    {
        $defaultAttributes = [
            'code' => 'VOUCHER'.uniqid(),
            'type' => 'nominal',
            'value' => 10000,
            'min_purchase' => 50000,
            'max_usage' => 100,
            'used_count' => 0,
            'expired_at' => now()->addDays(7),
        ];

        return Voucher::create(array_merge($defaultAttributes, $attributes));
    }

    public function createOrder(array $attributes = [])
    {
        $defaultAttributes = [
            'user_id' => User::factory()->create()->id,
            'product_id' => Product::firstOrCreate(['slug' => 'test-product'], ['name' => 'Test Product', 'category_id' => Category::firstOrCreate(['slug' => 'cat'], ['name' => 'cat'])->id])->id,
            'quantity' => 1,
            'price' => 10000,
            'discount' => 0,
            'total' => 10000,
            'payment_method' => 'balance',
            'payment_status' => 'pending',
            'status' => 'pending',
            'invoice_number' => 'INV-TEST-' . uniqid(),
        ];

        return \App\Models\Order::create(array_merge($defaultAttributes, $attributes));
    }

    public function createDeposit(array $attributes = [])
    {
        $defaultAttributes = [
            'user_id' => User::factory()->create()->id,
            'amount' => 50000,
            'payment_status' => 'pending',
        ];

        return \App\Models\Deposit::create(array_merge($defaultAttributes, $attributes));
    }
}
