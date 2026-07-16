<?php

use App\Actions\EquationQuest\EquationSolver;
use App\Actions\EquationQuest\GenerateEquationQuestPuzzle;
use App\Models\EquationPuzzle;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

/**
 * Seed a controlled puzzle for today so the component loads it deterministically.
 *
 * @param  list<int>  $numbers
 */
function seedTodaysPuzzle(array $numbers, int $target, string $solution = 'n/a'): EquationPuzzle
{
    return EquationPuzzle::factory()->create([
        'puzzle_date' => Carbon::today(),
        'numbers' => $numbers,
        'target' => $target,
        'solution' => $solution,
    ]);
}

test('guests are redirected away from equation quest', function () {
    $this->get(route('games.equation-quest'))->assertRedirect(route('login'));
});

test('the generator always produces a target reachable left to right', function () {
    $puzzle = app(GenerateEquationQuestPuzzle::class)->handle(Carbon::parse('2026-03-14'));

    $operators = (new EquationSolver)->solve($puzzle->numbers, $puzzle->target);

    expect($puzzle->numbers)->toHaveCount(6)
        ->and($puzzle->target)->toBeGreaterThanOrEqual(101)->toBeLessThanOrEqual(999)
        ->and($operators)->not->toBeNull()
        ->and((new EquationSolver)->evaluate($puzzle->numbers, $operators))->toBe($puzzle->target);
});

test('the generator is deterministic per date and idempotent', function () {
    $first = app(GenerateEquationQuestPuzzle::class)->handle(Carbon::parse('2026-03-14'), force: true);
    $second = app(GenerateEquationQuestPuzzle::class)->handle(Carbon::parse('2026-03-14'), force: true);

    expect($second->numbers)->toBe($first->numbers)
        ->and($second->target)->toBe($first->target)
        ->and(EquationPuzzle::query()->count())->toBe(1);
});

test('the command persists the daily puzzle', function () {
    $this->artisan('equation-quest:generate', ['--date' => '2026-03-14'])
        ->assertSuccessful();

    expect(EquationPuzzle::query()->whereDate('puzzle_date', '2026-03-14')->exists())->toBeTrue();
});

test('the board loads todays target with a gap for each pair', function () {
    seedTodaysPuzzle([100, 50, 25, 4, 3, 2], 184);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::equation-quest')
        ->assertSet('target', 184)
        ->assertCount('operators', 5)
        ->assertSee('184');
});

test('filling every gap to hit the target wins the game', function () {
    seedTodaysPuzzle([1, 2, 3, 4, 5, 6], 21, '1 + 2 + 3 + 4 + 5 + 6');

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::equation-quest')
        ->call('setOperator', '+')
        ->call('setOperator', '+')
        ->call('setOperator', '+')
        ->call('setOperator', '+')
        ->assertSet('won', false)
        ->call('setOperator', '+')
        ->assertSet('won', true)
        ->assertSet('operators', ['+', '+', '+', '+', '+']);
});

test('a non-whole division is flagged and does not win', function () {
    seedTodaysPuzzle([7, 2, 3, 4, 5, 6], 999);

    $this->actingAs(User::factory()->create());

    $component = Livewire::test('pages::equation-quest')
        ->call('setOperator', '/')      // 7 / 2 is not whole
        ->assertSet('won', false);

    expect($component->get('message'))->not->toBe('');
});

test('clearing a gap empties just that slot', function () {
    seedTodaysPuzzle([1, 2, 3, 4, 5, 6], 21);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::equation-quest')
        ->call('setOperator', '+')
        ->assertSet('operators', ['+', null, null, null, null])
        ->call('clearSlot', 0)
        ->assertSet('operators', [null, null, null, null, null]);
});
