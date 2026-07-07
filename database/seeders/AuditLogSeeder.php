<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();

        $entries = [
            // Product changes
            ['model_type' => 'App\\Models\\Product', 'model_id' => 1, 'action' => 'created', 'old_values' => null, 'new_values' => ['name' => 'Netflix Premium 1 Bulan', 'status' => 'active', 'price_customer' => 55000]],
            ['model_type' => 'App\\Models\\Product', 'model_id' => 2, 'action' => 'updated', 'old_values' => ['price_customer' => 18000], 'new_values' => ['price_customer' => 20000]],
            ['model_type' => 'App\\Models\\Product', 'model_id' => 3, 'action' => 'updated', 'old_values' => ['status' => 'inactive'], 'new_values' => ['status' => 'active']],
            ['model_type' => 'App\\Models\\Product', 'model_id' => 4, 'action' => 'created', 'old_values' => null, 'new_values' => ['name' => 'Free Fire 70 Diamonds', 'status' => 'active']],
            ['model_type' => 'App\\Models\\Product', 'model_id' => 5, 'action' => 'updated', 'old_values' => ['price_reseller' => 100000], 'new_values' => ['price_reseller' => 105000]],

            // User changes
            ['model_type' => 'App\\Models\\User', 'model_id' => 2, 'action' => 'updated', 'old_values' => ['name' => 'Budi S'], 'new_values' => ['name' => 'Budi Santoso']],
            ['model_type' => 'App\\Models\\User', 'model_id' => 3, 'action' => 'created', 'old_values' => null, 'new_values' => ['name' => 'Siti Rahayu', 'email' => 'siti@demo.com']],
            ['model_type' => 'App\\Models\\User', 'model_id' => 4, 'action' => 'updated', 'old_values' => ['balance' => 0], 'new_values' => ['balance' => 100000]],
            ['model_type' => 'App\\Models\\User', 'model_id' => 5, 'action' => 'created', 'old_values' => null, 'new_values' => ['name' => 'Dewi Lestari', 'email' => 'dewi@demo.com']],
            ['model_type' => 'App\\Models\\User', 'model_id' => 3, 'action' => 'updated', 'old_values' => ['email' => 'siti.old@demo.com'], 'new_values' => ['email' => 'siti@demo.com']],

            // Stock changes
            ['model_type' => 'App\\Models\\Stock', 'model_id' => 1, 'action' => 'created', 'old_values' => null, 'new_values' => ['product_id' => 1, 'status' => 'available']],
            ['model_type' => 'App\\Models\\Stock', 'model_id' => 5, 'action' => 'updated', 'old_values' => ['status' => 'available'], 'new_values' => ['status' => 'sold']],
            ['model_type' => 'App\\Models\\Stock', 'model_id' => 10, 'action' => 'deleted', 'old_values' => ['product_id' => 2, 'status' => 'available', 'value' => 'expired-account@example.com|pass123'], 'new_values' => null],
            ['model_type' => 'App\\Models\\Stock', 'model_id' => 15, 'action' => 'updated', 'old_values' => ['status' => 'available'], 'new_values' => ['status' => 'sold']],
            ['model_type' => 'App\\Models\\Stock', 'model_id' => 20, 'action' => 'created', 'old_values' => null, 'new_values' => ['product_id' => 3, 'status' => 'available']],
        ];

        foreach ($entries as $index => $entry) {
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));

            AuditLog::create([
                ...$entry,
                'user_id' => $admin->id,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
