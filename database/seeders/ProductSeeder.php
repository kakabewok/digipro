<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        $products = [
            [
                'category_slug' => 'streaming',
                'name' => 'Netflix Premium 1 Bulan',
                'price_customer' => 55000,
                'price_reseller' => 48000,
                'price_bulk' => 42000,
                'min_bulk_qty' => 5,
            ],
            [
                'category_slug' => 'streaming',
                'name' => 'Spotify Premium 1 Bulan',
                'price_customer' => 20000,
                'price_reseller' => 17000,
                'price_bulk' => 14000,
                'min_bulk_qty' => 5,
            ],
            [
                'category_slug' => 'game-voucher',
                'name' => 'Mobile Legends 86 Diamonds',
                'price_customer' => 25000,
                'price_reseller' => 22000,
                'price_bulk' => 19000,
                'min_bulk_qty' => 10,
            ],
            [
                'category_slug' => 'game-voucher',
                'name' => 'Free Fire 70 Diamonds',
                'price_customer' => 15000,
                'price_reseller' => 13000,
                'price_bulk' => 11000,
                'min_bulk_qty' => 10,
            ],
            [
                'category_slug' => 'productivity',
                'name' => 'Microsoft 365 Personal',
                'price_customer' => 120000,
                'price_reseller' => 105000,
                'price_bulk' => 95000,
                'min_bulk_qty' => 3,
            ],
            [
                'category_slug' => 'productivity',
                'name' => 'Canva Pro 1 Bulan',
                'price_customer' => 65000,
                'price_reseller' => 57000,
                'price_bulk' => 50000,
                'min_bulk_qty' => 5,
            ],
            [
                'category_slug' => 'education',
                'name' => 'Duolingo Plus 1 Bulan',
                'price_customer' => 45000,
                'price_reseller' => 39000,
                'price_bulk' => 34000,
                'min_bulk_qty' => 5,
            ],
            [
                'category_slug' => 'education',
                'name' => 'Coursera Plus 1 Bulan',
                'price_customer' => 200000,
                'price_reseller' => 180000,
                'price_bulk' => 160000,
                'min_bulk_qty' => 3,
            ],
            [
                'category_slug' => 'vpn-security',
                'name' => 'NordVPN 1 Bulan',
                'price_customer' => 80000,
                'price_reseller' => 70000,
                'price_bulk' => 62000,
                'min_bulk_qty' => 5,
            ],
            [
                'category_slug' => 'vpn-security',
                'name' => 'ExpressVPN 1 Bulan',
                'price_customer' => 95000,
                'price_reseller' => 83000,
                'price_bulk' => 74000,
                'min_bulk_qty' => 5,
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            unset($productData['category_slug']);

            Product::create([
                ...$productData,
                'category_id' => $categories[$categorySlug],
                'slug' => Str::slug($productData['name']),
                'thumbnail' => null,
                'status' => 'active',
            ]);
        }
    }
}
