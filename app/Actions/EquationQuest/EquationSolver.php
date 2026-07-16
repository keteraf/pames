<?php

namespace App\Actions\EquationQuest;

/**
 * Evaluates and searches fixed-order equations built by dropping an operator
 * into each gap between the dealt numbers, e.g. 100 + 6 - 6 * 50 ...
 *
 * Numbers keep their dealt order and the expression is read strictly
 * left-to-right (no operator precedence). Every intermediate result must be a
 * whole number, so a division is only valid when it divides evenly.
 */
class EquationSolver
{
    public const OPERATORS = ['+', '-', '*', '/'];

    /**
     * Evaluate an operator assignment left-to-right.
     *
     * Returns null if any division is not exact.
     *
     * @param  list<int>  $numbers
     * @param  list<string>  $operators
     */
    public function evaluate(array $numbers, array $operators): ?int
    {
        $total = $numbers[0];

        foreach ($operators as $index => $operator) {
            $next = $numbers[$index + 1];

            $total = match ($operator) {
                '+' => $total + $next,
                '-' => $total - $next,
                '*' => $total * $next,
                '/' => $next !== 0 && $total % $next === 0 ? intdiv($total, $next) : null,
                default => null,
            };

            if ($total === null) {
                return null;
            }
        }

        return $total;
    }

    /**
     * Map every reachable target to an operator assignment that produces it.
     *
     * @param  list<int>  $numbers
     * @return array<int, list<string>>
     */
    public function reachableTargets(array $numbers): array
    {
        $gaps = count($numbers) - 1;
        $combinations = 4 ** $gaps;
        $found = [];

        for ($mask = 0; $mask < $combinations; $mask++) {
            $operators = [];
            $remaining = $mask;

            for ($gap = 0; $gap < $gaps; $gap++) {
                $operators[] = self::OPERATORS[$remaining % 4];
                $remaining = intdiv($remaining, 4);
            }

            $value = $this->evaluate($numbers, $operators);

            if ($value !== null && ! array_key_exists($value, $found)) {
                $found[$value] = $operators;
            }
        }

        return $found;
    }

    /**
     * Find an operator assignment that reaches the target, or null.
     *
     * @param  list<int>  $numbers
     * @return list<string>|null
     */
    public function solve(array $numbers, int $target): ?array
    {
        return $this->reachableTargets($numbers)[$target] ?? null;
    }
}
