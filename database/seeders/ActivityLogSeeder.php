<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $actions = ['login', 'purchase', 'deposit', 'view_product', 'logout'];

        $amounts = ['Rp 50.000', 'Rp 100.000', 'Rp 150.000', 'Rp 200.000'];

        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $action = fake()->randomElement($actions);

            $description = match ($action) {
                'login' => "User {$user->email} berhasil login ke sistem",
                'purchase' => "User {$user->email} melakukan pembelian {$products->random()->name}",
                'deposit' => "User {$user->email} melakukan deposit sebesar " . fake()->randomElement($amounts),
                'view_product' => "User {$user->email} melihat produk {$products->random()->name}",
                'logout' => "User {$user->email} logout dari sistem",
            };

            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
