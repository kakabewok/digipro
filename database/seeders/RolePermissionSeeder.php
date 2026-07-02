<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Customer permissions
            'view products',
            'create order',
            'create deposit',
            'use voucher',
            'view own orders',
            'view own deposits',
            'edit own profile',

            // Reseller permissions
            'view reseller price',
            'view bulk price',

            // Admin permissions
            'manage products',
            'manage categories',
            'manage stocks',
            'import stocks',
            'manage vouchers',
            'manage users',
            'manage resellers',
            'manage deposits',
            'manage orders',
            'manage settings',
            'manage backups',
            'view logs',
            'view activity logs',
            'view audit logs',
            'toggle maintenance mode',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->syncPermissions([
            'view products',
            'create order',
            'create deposit',
            'use voucher',
            'view own orders',
            'view own deposits',
            'edit own profile',
        ]);

        $resellerRole = Role::firstOrCreate(['name' => 'reseller']);
        $resellerRole->syncPermissions([
            // All customer permissions
            'view products',
            'create order',
            'create deposit',
            'use voucher',
            'view own orders',
            'view own deposits',
            'edit own profile',
            // Plus reseller-specific
            'view reseller price',
            'view bulk price',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());
    }
}
