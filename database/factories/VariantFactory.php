<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $stock = $this->faker->numberBetween(0, 100);
        $possibleColors = ['red', 'green', 'blue'];
        $possibleSizes = ['small', 'medium', 'large'];

        $option1 = $this->faker->randomElement($possibleColors);
        $option2 = $this->faker->randomElement($possibleSizes);
        $product = Product::factory()->create();
        return [
            'product_id' => $product->id,
            'title' => $option1 . $option2 . $product->title,
            'option1' => $option1,
            'option2' => $option2,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $stock,
            'is_in_stock' => $stock > 0 ? true : false,
        
        ];
    }
}
