<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Streaming', 'slug' => 'streaming'],
            ['name' => 'Game Voucher', 'slug' => 'game-voucher'],
            ['name' => 'Productivity', 'slug' => 'productivity'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'VPN & Security', 'slug' => 'vpn-security'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
