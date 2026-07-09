<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_admin_can_view_user_list()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_promote_customer_to_reseller()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)->put('/admin/users/' . $customer->id . '/role', [
            'role' => 'reseller'
        ]);

        $response->assertRedirect();
        
        $customer->refresh();
        $this->assertTrue($customer->hasRole('reseller'));
        $this->assertFalse($customer->hasRole('customer')); // Assuming role is replaced
    }

    public function test_admin_can_demote_reseller_to_customer()
    {
        $admin = $this->createAdmin();
        $reseller = $this->createReseller();

        $response = $this->actingAs($admin)->put('/admin/users/' . $reseller->id . '/role', [
            'role' => 'customer'
        ]);

        $response->assertRedirect();
        
        $reseller->refresh();
        $this->assertTrue($reseller->hasRole('customer'));
        $this->assertFalse($reseller->hasRole('reseller'));
    }

    public function test_promoted_user_gets_reseller_permissions()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        
        // Ensure reseller role has the permission first
        $role = Role::firstOrCreate(['name' => 'reseller', 'guard_name' => 'web']);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $this->actingAs($admin)->put('/admin/users/' . $customer->id . '/role', [
            'role' => 'reseller'
        ]);
        
        $customer->refresh();
        $this->assertTrue($customer->can('view reseller price'));
    }

    public function test_demoted_user_loses_reseller_permissions()
    {
        $admin = $this->createAdmin();
        $reseller = $this->createReseller();
        
        $role = Role::firstOrCreate(['name' => 'reseller', 'guard_name' => 'web']);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view reseller price', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        
        // Sanity check
        $this->assertTrue($reseller->can('view reseller price'));

        $this->actingAs($admin)->put('/admin/users/' . $reseller->id . '/role', [
            'role' => 'customer'
        ]);
        
        $reseller->refresh();
        $this->assertFalse($reseller->can('view reseller price'));
    }

    public function test_admin_can_reset_user_password()
    {
        $admin = $this->createAdmin();
        $user = $this->createCustomer();
        $oldPasswordHash = $user->password;

        $response = $this->actingAs($admin)->put('/admin/users/' . $user->id . '/reset-password', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_admin_can_delete_user()
    {
        $admin = $this->createAdmin();
        $user = $this->createCustomer();

        $response = $this->actingAs($admin)->delete('/admin/users/' . $user->id);

        $response->assertRedirect();
        
        // Usually soft deletes
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_themselves()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->delete('/admin/users/' . $admin->id);

        // Usually redirects with error or 403
        $this->assertTrue($response->isRedirect() || $response->status() === 403);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_customer_list_does_not_show_admin_accounts()
    {
        $admin1 = $this->createAdmin();
        $admin2 = $this->createAdmin(['name' => 'Super Secret Admin']);
        $customer = $this->createCustomer(['name' => 'Normal User']);

        $response = $this->actingAs($admin1)->get('/admin/users');

        $response->assertSee('Normal User');
        
        // Depending on UI logic, sometimes admin accounts are hidden from user list to prevent accidental changes
        // Or if there's a specific filter for role=customer
        // In the prompt: "customer list does not show admin accounts"
        $response = $this->actingAs($admin1)->get('/admin/users?role=customer');
        $response->assertDontSee('Super Secret Admin');
    }
}
