<?php

namespace Database\Factories;

use App\Models\Category; // <-- Import Category
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true), // e.g., "Spicy Basil Chicken"
            'description' => fake()->sentence(10),
            'price' => fake()->numberBetween(40, 350), // Price between 40 and 350

            // This line automatically creates a new Category for this item,
            // or finds an existing one.
            'category_id' => Category::factory(),

            'is_available' => true,
        ];
    }
}
