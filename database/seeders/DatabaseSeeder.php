<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin DigiPro',
            'email' => 'admin@digipro.test',
        ]);
        $admin->assignRole('admin');

        // Create test customer user
        $customer = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $customer->assignRole('customer');

        // Create test reseller user
        $reseller = User::factory()->create([
            'name' => 'Test Reseller',
            'email' => 'reseller@example.com',
        ]);
        $reseller->assignRole('reseller');

        // Seed default website settings
        $defaultSettings = [
            'site_name' => 'DigiPro',
            'site_description' => 'Digital Product Store',
            'admin_contact' => 'admin@digipro.test',
            'low_stock_threshold' => '5',
            'maintenance_mode' => '0',
        ];

        foreach ($defaultSettings as $key => $value) {
            WebsiteSetting::set($key, $value);
        }
    }
}
