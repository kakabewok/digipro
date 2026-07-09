<?php

namespace Database\Factories;

use App\Models\WebsiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebsiteSettingFactory extends Factory
{
    protected $model = WebsiteSetting::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word,
            'value' => $this->faker->word,
        ];
    }
}
