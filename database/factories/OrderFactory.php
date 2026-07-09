<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => \App\Models\Product::factory(),
            'quantity' => 1,
            'price' => 10000,
            'discount' => 0,
            'total' => 10000,
            'payment_method' => 'balance',
            'payment_status' => 'pending',
            'status' => 'pending',
            'invoice_number' => 'INV-' . $this->faker->unique()->numerify('########-######'),
        ];
    }
}
