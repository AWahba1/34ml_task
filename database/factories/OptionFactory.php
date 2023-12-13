<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $possibleColors = ['red', 'green', 'blue'];
        $possibleSizes = ['small', 'medium', 'large'];
        $possibleNames = ['color', 'size'];

        $randomName = $this->faker->randomElement($possibleNames);
        $possibleValues = $randomName === 'color' ? $possibleColors : $possibleSizes;

        $numberOfValues = $this->faker->numberBetween(1, count($possibleValues));

        $selectedValues = $this->faker->randomElements($possibleValues, $numberOfValues);

        return [
            'name' => $randomName,
            'values' => $selectedValues,
        ];
    }
}
