<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(implode(' ', Arr::wrap(fake()->unique()->words(fake()->numberBetween(2, 3)))));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'genre' => fake()->randomElement(['Logic', 'Math', 'Arithmetic', 'Pattern', 'Memory']),
            'description' => fake()->sentence(12),
            'sprite' => fake()->randomElement(['🔢', '🧩', '➗', '🖼️', '🗼', '🔲', '📈', '💡']),
            'accent_color' => fake()->randomElement(['#7c3aed', '#22d3ee', '#f59e0b', '#ec4899', '#10b981', '#ef4444']),
            'difficulty' => fake()->randomElement(['Easy', 'Medium', 'Hard']),
            'is_featured' => false,
        ];
    }

    /**
     * Indicate that the game is featured on the arcade grid.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_featured' => true,
        ]);
    }
}
