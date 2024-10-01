<?php

namespace Database\Factories;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'produit_id' => fake()->randomElement([1, 2, 3, 4, 5, 6]),
            'congelateur' => fake()->randomElement(['Grand', 'Petit', 'Menimur']),
            'poids' => fake()->numberBetween(1, 50) * 50,
            'etage' => fake()->randomElement([1, 2, 3, 4, 5, 6, 7]),
            'created_at' => fake()->dateTimeBetween('-4 year', 'now')
        ];
    }
}
