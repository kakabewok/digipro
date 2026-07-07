<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::with('category')->get();

        foreach ($products as $product) {
            $categorySlug = $product->category->slug;

            for ($i = 0; $i < 20; $i++) {
                $value = match ($categorySlug) {
                    'streaming' => fake()->unique()->safeEmail() . '|' . fake()->password(8, 12),
                    'game-voucher' => 'VOUCHER-' . strtoupper(fake()->bothify('????-####-????')),
                    default => fake()->unique()->safeEmail() . '|' . fake()->password(8, 12) . '|profile: slot ' . rand(1, 5),
                };

                Stock::create([
                    'product_id' => $product->id,
                    'value' => $value,
                    'status' => 'available',
                    'sold_at' => null,
                ]);
            }
        }
    }
}
