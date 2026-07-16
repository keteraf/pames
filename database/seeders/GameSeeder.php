<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GameSeeder extends Seeder
{
    /**
     * Seed the arcade with a hand-crafted line-up of retro titles.
     */
    public function run(): void
    {
        $games = [
            ['title' => 'Prime Hunter', 'genre' => 'Math', 'sprite' => '🔢', 'accent_color' => '#7c3aed', 'difficulty' => 'Medium', 'is_featured' => true, 'description' => 'Sift the primes from the crowd before the timer runs dry. Every wrong tap costs a life.'],
            ['title' => 'Sudoku Sprint', 'genre' => 'Logic', 'sprite' => '🧩', 'accent_color' => '#22d3ee', 'difficulty' => 'Hard', 'is_featured' => true, 'description' => 'Classic 9×9 deduction against the clock. No guessing — every square has one true answer.'],
            ['title' => 'Equation Quest', 'genre' => 'Arithmetic', 'sprite' => '➗', 'accent_color' => '#f59e0b', 'difficulty' => 'Easy', 'is_featured' => true, 'description' => 'Drop + − × ÷ into the gaps between six numbers and read left to right to hit the daily target.'],
            ['title' => 'Nonogram Nights', 'genre' => 'Logic', 'sprite' => '🖼️', 'accent_color' => '#ec4899', 'difficulty' => 'Medium', 'description' => 'Read the row and column clues to reveal a hidden pixel picture, one deduction at a time.'],
            ['title' => 'Tower of Hanoi', 'genre' => 'Logic', 'sprite' => '🗼', 'accent_color' => '#10b981', 'difficulty' => 'Easy', 'description' => 'Move the whole stack to the far peg — never placing a larger disc on a smaller one.'],
            ['title' => 'Magic Square', 'genre' => 'Math', 'sprite' => '🔲', 'accent_color' => '#22d3ee', 'difficulty' => 'Hard', 'description' => 'Arrange the digits so every row, column, and diagonal sums to the very same number.'],
            ['title' => 'Sequence Seeker', 'genre' => 'Pattern', 'sprite' => '📈', 'accent_color' => '#ef4444', 'difficulty' => 'Medium', 'description' => 'Spot the rule behind the run of numbers and predict what comes next before time expires.'],
            ['title' => 'Lights Out', 'genre' => 'Logic', 'sprite' => '💡', 'accent_color' => '#7c3aed', 'difficulty' => 'Hard', 'description' => 'Toggle tiles to switch off every light — but each press flips its neighbours too.'],
        ];

        foreach ($games as $game) {
            $slug = Str::slug($game['title']);

            Game::query()->updateOrCreate(['slug' => $slug], [...$game, 'slug' => $slug]);
        }
    }
}
