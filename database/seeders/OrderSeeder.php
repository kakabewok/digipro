<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get customer and reseller users
        $users = User::role(['customer', 'reseller'])->get();
        $products = Product::all();

        // 20 completed orders
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $product = $products->random();

            // Pick an available stock for this product
            $stock = Stock::where('product_id', $product->id)
                ->where('status', 'available')
                ->first();

            if (! $stock) {
                continue;
            }

            // Mark stock as sold
            $stock->update([
                'status' => 'sold',
                'sold_at' => now(),
            ]);

            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'stock_id' => $stock->id,
                'voucher_id' => null,
                'quantity' => 1,
                'price' => $product->price_customer,
                'discount' => 0,
                'total' => $product->price_customer,
                'payment_method' => fake()->randomElement(['balance', 'qris']),
                'payment_status' => 'paid',
                'status' => 'completed',
                'invoice_number' => 'INV-' . $createdAt->format('Ymd') . '-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'notes' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // 3 pending orders
        for ($i = 0; $i < 3; $i++) {
            $user = $users->random();
            $product = $products->random();

            $createdAt = now()->subDays(rand(0, 5))->subHours(rand(0, 23));

            Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'stock_id' => null,
                'voucher_id' => null,
                'quantity' => 1,
                'price' => $product->price_customer,
                'discount' => 0,
                'total' => $product->price_customer,
                'payment_method' => 'qris',
                'payment_status' => 'pending',
                'status' => 'pending',
                'invoice_number' => 'INV-' . $createdAt->format('Ymd') . '-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'notes' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
