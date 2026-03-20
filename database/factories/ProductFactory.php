<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'           => fake()->words(2, true),
            'description'    => fake()->sentence(),
            'price'          => fake()->randomFloat(2, 1, 1000),
            'stock_quantity' => fake()->numberBetween(1, 100)
        ];
    }
}
