<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::firstOrCreate(['slug' => 'default'], ['name' => 'Default Category'])->id,
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug,
            'price_customer' => 100000,
            'price_reseller' => 80000,
            'price_bulk' => 70000,
            'min_bulk_qty' => 10,
            'status' => 'active',
        ];
    }
}
