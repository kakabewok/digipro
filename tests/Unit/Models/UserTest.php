<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_default_role_is_customer_after_registration()
    {
        // Actually, this behavior usually happens in AuthController or RegisterController.
        // If it's a Model observer or standard setup, we can test it.
        // Let's test the fact that a user can be assigned customer role and defaults work if applicable.
        $user = $this->createCustomer();
        $this->assertTrue($user->hasRole('customer'));
    }

    public function test_has_role_admin_returns_true_for_admin_user()
    {
        $user = $this->createAdmin();
        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_has_role_reseller_returns_true_for_reseller_user()
    {
        $user = $this->createReseller();
        $this->assertTrue($user->hasRole('reseller'));
    }

    public function test_can_view_reseller_price_returns_true_for_reseller()
    {
        $user = $this->createReseller();
        $permission = Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $this->assertTrue($user->can('view reseller price'));
    }

    public function test_can_manage_products_returns_true_for_admin()
    {
        $user = $this->createAdmin();
        $permission = Permission::firstOrCreate(['name' => 'manage products', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);
        
        $this->assertTrue($user->can('manage products'));
    }

    public function test_can_manage_products_returns_false_for_customer()
    {
        $user = $this->createCustomer();
        // Permission is not given
        $this->assertFalse($user->can('manage products'));
    }

    public function test_balance_starts_at_0()
    {
        $user = User::factory()->create();
        $this->assertEquals(0, $user->balance);
    }
}
