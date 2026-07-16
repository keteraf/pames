<?php

namespace Database\Factories;

use App\Models\EquationPuzzle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EquationPuzzle>
 */
class EquationPuzzleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $numbers = [100, 50, 25, 4, 3, 2];

        return [
            'puzzle_date' => fake()->unique()->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d'),
            'numbers' => $numbers,
            'target' => array_sum($numbers),
            'solution' => '100 + 50 + 25 + 4 + 3 + 2',
        ];
    }
}
