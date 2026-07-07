<?php

namespace Database\Seeders;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepositSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::role(['customer', 'reseller'])->get();
        $amounts = [50000, 100000, 150000, 200000];

        // 10 paid deposits
        for ($i = 0; $i < 10; $i++) {
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));

            Deposit::create([
                'user_id' => $users->random()->id,
                'amount' => fake()->randomElement($amounts),
                'payment_status' => 'paid',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // 3 pending deposits
        for ($i = 0; $i < 3; $i++) {
            $createdAt = now()->subDays(rand(0, 5))->subHours(rand(0, 12));

            Deposit::create([
                'user_id' => $users->random()->id,
                'amount' => fake()->randomElement($amounts),
                'payment_status' => 'pending',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // 2 expired deposits
        for ($i = 0; $i < 2; $i++) {
            $createdAt = now()->subDays(rand(10, 30))->subHours(rand(0, 23));

            Deposit::create([
                'user_id' => $users->random()->id,
                'amount' => fake()->randomElement($amounts),
                'payment_status' => 'expired',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
