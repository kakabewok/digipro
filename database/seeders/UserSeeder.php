<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@demo.com',
                'role' => 'admin',
                'balance' => 0,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@demo.com',
                'role' => 'reseller',
                'balance' => rand(150000, 500000),
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti@demo.com',
                'role' => 'customer',
                'balance' => rand(50000, 300000),
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi@demo.com',
                'role' => 'customer',
                'balance' => rand(50000, 300000),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@demo.com',
                'role' => 'reseller',
                'balance' => rand(150000, 500000),
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::create([
                ...$userData,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($role);
        }
    }
}
