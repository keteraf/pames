<?php

use App\Actions\EquationQuest\GenerateEquationQuestPuzzle;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Equation Quest')] class extends Component {
    public string $puzzleDate = '';

    public int $target = 0;

    public string $solution = '';

    /** @var list<int> */
    public array $seedNumbers = [];

    /** @var list<string|null> */
    public array $operators = [];

    public ?int $activeSlot = 0;

    public bool $won = false;

    public bool $showSolution = false;

    public string $message = '';

    /**
     * Load (lazily generating if needed) today's puzzle and empty the gaps.
     */
    public function mount(GenerateEquationQuestPuzzle $generate): void
    {
        $puzzle = $generate->handle(Carbon::today());

        $this->puzzleDate = $puzzle->puzzle_date->toDateString();
        $this->target = $puzzle->target;
        $this->solution = $puzzle->solution;
        $this->seedNumbers = $puzzle->numbers;

        $this->startFresh();
    }

    /**
     * Clear every gap and start over.
     */
    public function startFresh(): void
    {
        $this->operators = array_fill(0, max(0, count($this->seedNumbers) - 1), null);
        $this->activeSlot = 0;
        $this->won = false;
        $this->showSolution = false;
        $this->message = '';
    }

    /**
     * Focus a gap so the next operator lands there.
     */
    public function selectSlot(int $slot): void
    {
        if ($this->won || ! array_key_exists($slot, $this->operators)) {
            return;
        }

        $this->activeSlot = $slot;
    }

    /**
     * Drop an operator into the active gap, then advance to the next empty gap.
     */
    public function setOperator(string $operator): void
    {
        if ($this->won || ! in_array($operator, ['+', '-', '*', '/'], true)) {
            return;
        }

        $slot = $this->activeSlot ?? $this->firstEmptySlot();

        if ($slot === null || ! array_key_exists($slot, $this->operators)) {
            return;
        }

        $this->operators[$slot] = $operator;
        $this->activeSlot = $this->firstEmptySlot();

        $this->refreshState();
    }

    /**
     * Empty a single gap.
     */
    public function clearSlot(int $slot): void
    {
        if ($this->won || ! array_key_exists($slot, $this->operators)) {
            return;
        }

        $this->operators[$slot] = null;
        $this->activeSlot = $slot;

        $this->refreshState();
    }

    /**
     * Reveal the known solving expression.
     */
    public function revealSolution(): void
    {
        $this->showSolution = true;
    }

    /**
     * The running left-to-right evaluation of the gaps filled so far.
     *
     * @return array{value: int|null, complete: bool, valid: bool}
     */
    #[Computed]
    public function evaluation(): array
    {
        $total = $this->seedNumbers[0] ?? 0;

        foreach ($this->operators as $index => $operator) {
            if ($operator === null) {
                return ['value' => $total, 'complete' => false, 'valid' => true];
            }

            $next = $this->seedNumbers[$index + 1];

            if ($operator === '/') {
                if ($next === 0 || $total % $next !== 0) {
                    return ['value' => null, 'complete' => false, 'valid' => false];
                }

                $total = intdiv($total, $next);
            } else {
                $total = match ($operator) {
                    '+' => $total + $next,
                    '-' => $total - $next,
                    '*' => $total * $next,
                };
            }
        }

        return ['value' => $total, 'complete' => true, 'valid' => true];
    }

    /**
     * Recompute the win/feedback state after a change.
     */
    private function refreshState(): void
    {
        unset($this->evaluation);
        $evaluation = $this->evaluation;

        $this->won = $evaluation['complete'] && $evaluation['value'] === $this->target;

        $this->message = match (true) {
            ! $evaluation['valid'] => __('That division would not be whole — try another operator.'),
            $this->won => '',
            $evaluation['complete'] => __('That makes :value, not :target.', ['value' => $evaluation['value'], 'target' => $this->target]),
            default => '',
        };
    }

    /**
     * The index of the first empty gap, or null if every gap is filled.
     */
    private function firstEmptySlot(): ?int
    {
        foreach ($this->operators as $slot => $operator) {
            if ($operator === null) {
                return $slot;
            }
        }

        return null;
    }
}; ?>

<div class="mx-auto flex w-full max-w-3xl flex-col gap-8">
    {{-- Header --}}
    <header class="scanlines relative overflow-hidden rounded-2xl border-2 border-amber-500/40 bg-zinc-900 p-6 shadow-[0_0_0_2px_rgba(0,0,0,0.6),0_0_40px_-8px_rgba(245,158,11,0.6)] sm:p-8">
        <div class="pointer-events-none absolute -right-8 -top-8 text-[8rem] opacity-10 select-none">➗</div>
        <flux:button :href="route('dashboard')" wire:navigate variant="ghost" size="sm" icon="arrow-left" class="mb-4">
            {{ __('Arcade') }}
        </flux:button>
        <flux:heading size="xl" class="font-pixel !text-lg text-amber-300 drop-shadow-[0_2px_0_rgba(0,0,0,0.8)] sm:!text-2xl">
            {{ __('Equation Quest') }}
        </flux:heading>
        <flux:text class="mt-3 text-zinc-400">
            {{ __('Drop an operator (+ − × ÷) into every gap. The line is read left to right, and every step must stay a whole number.') }}
        </flux:text>
        <flux:text class="mt-1 text-xs text-zinc-600">{{ __('Daily puzzle · :date', ['date' => $puzzleDate]) }}</flux:text>
    </header>

    {{-- Target --}}
    <div class="flex flex-col items-center gap-2">
        <flux:text class="font-pixel text-xs uppercase tracking-widest text-zinc-500">{{ __('Target') }}</flux:text>
        <div @class([
            'font-pixel rounded-xl border-2 px-8 py-5 text-4xl transition-colors sm:text-5xl',
            'border-emerald-400 text-emerald-300 shadow-[0_0_30px_-4px_rgba(52,211,153,0.7)]' => $won,
            'border-amber-500/60 text-amber-300 shadow-[0_0_30px_-8px_rgba(245,158,11,0.6)]' => ! $won,
        ])>
            {{ $target }}
        </div>
    </div>

    {{-- Win banner --}}
    @if ($won)
        <div class="scanlines flex flex-col items-center gap-2 rounded-xl border-2 border-emerald-400/60 bg-emerald-950/40 py-6 text-center">
            <span class="text-4xl">🏆</span>
            <flux:heading class="font-pixel !text-sm text-emerald-300">{{ __('Solved!') }}</flux:heading>
            <flux:text class="text-emerald-200/70">{{ __('You hit :target exactly.', ['target' => $target]) }}</flux:text>
        </div>
    @endif

    {{-- The equation: numbers with operator gaps between them --}}
    @php($symbols = ['+' => '+', '-' => '−', '*' => '×', '/' => '÷'])
    <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
        @foreach ($seedNumbers as $i => $number)
            <div
                wire:key="num-{{ $i }}"
                class="font-pixel pixel-card flex size-16 items-center justify-center rounded-lg border-2 border-zinc-700 bg-zinc-900 text-lg text-zinc-100 sm:size-20 sm:text-xl"
            >
                {{ $number }}
            </div>

            @if ($i < count($seedNumbers) - 1)
                <button
                    type="button"
                    wire:key="slot-{{ $i }}"
                    wire:click="selectSlot({{ $i }})"
                    @disabled($won)
                    @class([
                        'font-pixel flex size-12 items-center justify-center rounded-lg border-2 text-lg transition-all duration-100 sm:size-14 sm:text-xl',
                        'border-amber-400 bg-amber-500/20 text-amber-200 shadow-[0_0_18px_-4px_rgba(245,158,11,0.8)]' => $activeSlot === $i && ! $won,
                        'border-violet-500/70 bg-violet-500/15 text-violet-200' => $operators[$i] !== null && $activeSlot !== $i,
                        'border-dashed border-zinc-700 bg-zinc-950 text-zinc-600' => $operators[$i] === null && $activeSlot !== $i,
                    ])
                >
                    {{ $operators[$i] !== null ? $symbols[$operators[$i]] : '?' }}
                </button>
            @endif
        @endforeach

        <div class="font-pixel px-1 text-lg text-zinc-500 sm:text-xl">=</div>
        <div class="font-pixel flex h-16 items-center justify-center rounded-lg border-2 border-amber-500/50 px-4 text-lg text-amber-300 sm:h-20 sm:text-xl">
            {{ $target }}
        </div>
    </div>

    {{-- Running total --}}
    <div class="flex flex-col items-center gap-1">
        <flux:text class="font-pixel text-[0.6rem] uppercase tracking-widest text-zinc-500">{{ __('Your total') }}</flux:text>
        @php($evaluation = $this->evaluation)
        <div @class([
            'font-pixel text-2xl',
            'text-red-400' => ! $evaluation['valid'],
            'text-emerald-300' => $evaluation['valid'] && $won,
            'text-zinc-200' => $evaluation['valid'] && ! $won,
        ])>
            {{ ! $evaluation['valid'] ? '✗' : $evaluation['value'] }}
        </div>
    </div>

    {{-- Operator palette --}}
    <div class="flex justify-center gap-3">
        @foreach ($symbols as $op => $label)
            <button
                type="button"
                wire:key="op-{{ $op }}"
                wire:click="setOperator('{{ $op }}')"
                @disabled($won)
                @class([
                    'font-pixel flex size-14 items-center justify-center rounded-lg border-2 text-xl transition-all duration-100',
                    'border-zinc-700 bg-zinc-900 text-zinc-200 hover:-translate-y-1 hover:border-violet-400/70' => ! $won,
                    'cursor-not-allowed border-zinc-800 bg-zinc-950 text-zinc-700 opacity-50' => $won,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Message --}}
    @if ($message !== '' && ! $won)
        <flux:text class="text-center text-sm text-amber-400/80">{{ $message }}</flux:text>
    @endif

    {{-- Controls --}}
    <div class="flex flex-wrap items-center justify-center gap-3">
        <flux:button wire:click="startFresh" variant="ghost" size="sm" icon="arrow-path">
            {{ __('Reset') }}
        </flux:button>
        <flux:button wire:click="revealSolution" variant="ghost" size="sm" icon="light-bulb" :disabled="$showSolution">
            {{ __('Reveal') }}
        </flux:button>
    </div>

    {{-- Solution reveal --}}
    @if ($showSolution)
        <div class="rounded-xl border-2 border-dashed border-amber-500/40 bg-amber-950/20 p-4 text-center">
            <flux:text class="font-pixel mb-2 text-[0.6rem] uppercase tracking-widest text-amber-500/70">{{ __('One solution') }}</flux:text>
            <flux:text class="font-mono text-amber-200">{{ $solution }} = {{ $target }}</flux:text>
        </div>
    @endif
</div>
