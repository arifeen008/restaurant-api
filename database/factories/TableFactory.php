<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Table>
 */
class TableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'T' . fake()->unique()->numberBetween(1, 50), // e.g., T1, T2, T3
            'capacity' => fake()->randomElement([2, 4, 4, 4, 6, 8]),
            'status' => 'available',
        ];
    }
}
