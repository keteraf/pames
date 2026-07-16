<?php

use App\Actions\EquationQuest\EquationSolver;

test('it evaluates operators strictly left to right', function () {
    // (2 + 3) * 4 = 20, not 2 + (3 * 4) = 14
    expect((new EquationSolver)->evaluate([2, 3, 4], ['+', '*']))->toBe(20);
});

test('it rejects divisions that are not whole', function () {
    expect((new EquationSolver)->evaluate([7, 2], ['/']))->toBeNull();
});

test('it accepts exact divisions', function () {
    expect((new EquationSolver)->evaluate([100, 5, 2], ['/', '/']))->toBe(10);
});

test('it finds an operator assignment for a reachable target', function () {
    $operators = (new EquationSolver)->solve([1, 2, 3, 4, 5, 6], 21);

    expect($operators)->toBe(['+', '+', '+', '+', '+']);
});

test('it returns null when no assignment reaches the target', function () {
    expect((new EquationSolver)->solve([1, 1, 1], 100))->toBeNull();
});
