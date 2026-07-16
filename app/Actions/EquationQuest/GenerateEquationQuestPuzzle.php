<?php

namespace App\Actions\EquationQuest;

use App\Models\EquationPuzzle;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Builds the guaranteed-solvable daily puzzle for a given date.
 *
 * Numbers are dealt Countdown-style (up to four "large" tiles plus small
 * tiles from one to ten), then the solver reveals every target reachable by
 * dropping operators into the gaps (evaluated left-to-right) so a genuine
 * target can be chosen. Generation is seeded by the date so re-running it is
 * deterministic and idempotent.
 */
class GenerateEquationQuestPuzzle
{
    private const LARGE_NUMBERS = [25, 50, 75, 100];

    private const MIN_TARGET = 101;

    private const MAX_TARGET = 999;

    public function __construct(private readonly EquationSolver $solver) {}

    /**
     * Generate (or return the existing) puzzle for the given date.
     */
    public function handle(Carbon $date, bool $force = false): EquationPuzzle
    {
        $key = $date->toDateString();

        $puzzle = EquationPuzzle::query()->whereDate('puzzle_date', $key)->first();

        if ($puzzle !== null && ! $force) {
            return $puzzle;
        }

        [$numbers, $target, $solution] = $this->roll($key);

        $puzzle ??= new EquationPuzzle;

        $puzzle->fill([
            'puzzle_date' => $key,
            'numbers' => $numbers,
            'target' => $target,
            'solution' => $solution,
        ])->save();

        return $puzzle;
    }

    /**
     * Deal numbers and pick a reachable target, retrying until one is found.
     *
     * @return array{list<int>, int, string}
     */
    private function roll(string $key): array
    {
        for ($attempt = 0; $attempt < 30; $attempt++) {
            mt_srand(crc32("{$key}:{$attempt}"));

            $numbers = $this->dealNumbers();
            $reachable = $this->solver->reachableTargets($numbers);

            $candidates = [];

            foreach (array_keys($reachable) as $value) {
                if ($value >= self::MIN_TARGET && $value <= self::MAX_TARGET) {
                    $candidates[] = $value;
                }
            }

            if ($candidates === []) {
                continue;
            }

            $target = $candidates[mt_rand(0, count($candidates) - 1)];
            mt_srand();

            return [$numbers, $target, $this->describe($numbers, $reachable[$target])];
        }

        mt_srand();

        throw new RuntimeException("Unable to generate an Equation Quest puzzle for {$key}.");
    }

    /**
     * Render an operator assignment as a readable left-to-right expression.
     *
     * @param  list<int>  $numbers
     * @param  list<string>  $operators
     */
    private function describe(array $numbers, array $operators): string
    {
        $symbols = ['+' => '+', '-' => '−', '*' => '×', '/' => '÷'];
        $parts = [(string) $numbers[0]];

        foreach ($operators as $index => $operator) {
            $parts[] = $symbols[$operator];
            $parts[] = (string) $numbers[$index + 1];
        }

        return implode(' ', $parts);
    }

    /**
     * Deal six numbers: a random count of large tiles, the rest small (1-10).
     *
     * @return list<int>
     */
    private function dealNumbers(): array
    {
        $large = self::LARGE_NUMBERS;
        shuffle($large);
        $largeCount = mt_rand(0, 4);
        $numbers = array_slice($large, 0, $largeCount);

        $small = [];

        for ($value = 1; $value <= 10; $value++) {
            $small[] = $value;
            $small[] = $value;
        }

        shuffle($small);
        $numbers = [...$numbers, ...array_slice($small, 0, 6 - $largeCount)];
        shuffle($numbers);

        return $numbers;
    }
}
