<?php

use App\Models\Game;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Arcade')] class extends Component {
    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $genre = '';

    /**
     * The games matching the current search and genre filter.
     *
     * @return Collection<int, Game>
     */
    #[Computed]
    public function games(): Collection
    {
        return Game::query()
            ->search($this->search)
            ->when(filled($this->genre), fn ($query) => $query->where('genre', $this->genre))
            ->arcadeOrder()
            ->get();
    }

    /**
     * The distinct genres available for filtering.
     *
     * @return Collection<int, string>
     */
    #[Computed]
    public function genres(): Collection
    {
        return Game::query()->distinct()->orderBy('genre')->pluck('genre');
    }

    /**
     * Reset the active genre filter.
     */
    public function clearGenre(): void
    {
        $this->genre = '';
    }
}; ?>

<div class="mx-auto flex w-full max-w-6xl flex-col gap-8">
    {{-- Marquee / title screen header --}}
    <header class="scanlines relative overflow-hidden rounded-2xl border-2 border-violet-500/40 bg-zinc-900 p-6 shadow-[0_0_0_2px_rgba(0,0,0,0.6),0_0_40px_-8px_rgba(124,58,237,0.6)] sm:p-8">
        <div class="pointer-events-none absolute -right-10 -top-10 text-[9rem] opacity-10 select-none">🧮</div>
        <flux:heading size="xl" class="font-pixel !text-lg text-violet-300 drop-shadow-[0_2px_0_rgba(0,0,0,0.8)] sm:!text-2xl">
            {{ __('Pick a Puzzle') }}
        </flux:heading>
        <flux:text class="mt-3 max-w-xl text-zinc-400">
            {{ __('Choose your challenge, :name. Sharpen your logic and flex those numbers.', ['name' => auth()->user()->name]) }}
        </flux:text>
    </header>

    {{-- Controls: search + genre filter --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <flux:input
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            placeholder="{{ __('Search titles or genres…') }}"
            class="max-w-xs"
        />

        <div class="flex flex-wrap items-center gap-2">
            <flux:button
                size="sm"
                variant="{{ $genre === '' ? 'primary' : 'ghost' }}"
                wire:click="clearGenre"
            >
                {{ __('All') }}
            </flux:button>

            @foreach ($this->genres as $availableGenre)
                <flux:button
                    :key="$availableGenre"
                    size="sm"
                    variant="{{ $genre === $availableGenre ? 'primary' : 'ghost' }}"
                    wire:click="$set('genre', '{{ $availableGenre }}')"
                >
                    {{ $availableGenre }}
                </flux:button>
            @endforeach
        </div>
    </div>

    {{-- The grid of games --}}
    <div
        wire:loading.class="opacity-40"
        wire:target="search, genre, clearGenre"
        class="grid grid-cols-1 gap-5 transition-opacity sm:grid-cols-2 lg:grid-cols-3"
    >
        @forelse ($this->games as $game)
            <article
                wire:key="game-{{ $game->id }}"
                style="--accent: {{ $game->accent_color }};"
                class="pixel-card group relative flex flex-col overflow-hidden rounded-xl border-2 border-zinc-700 bg-zinc-900/80 p-5 transition-all duration-150 hover:-translate-y-1 hover:border-[var(--accent)] hover:shadow-[0_0_28px_-6px_var(--accent)]"
            >
                @if ($game->is_featured)
                    <flux:badge size="sm" color="amber" class="font-pixel absolute right-3 top-3 !text-[0.55rem]">
                        {{ __('Featured') }}
                    </flux:badge>
                @endif

                <div class="pixel-sprite mb-4 flex size-20 items-center justify-center rounded-lg border-2 border-[var(--accent)]/50 bg-black/40 text-4xl">
                    {{ $game->sprite }}
                </div>

                <flux:heading class="font-pixel !text-sm leading-snug text-zinc-100">
                    {{ $game->title }}
                </flux:heading>

                <div class="mt-1 text-xs">
                    <span class="text-[var(--accent)]">{{ $game->genre }}</span>
                </div>

                <flux:text class="mt-3 line-clamp-3 flex-1 text-sm text-zinc-400">
                    {{ $game->description }}
                </flux:text>

                <div class="mt-5 flex items-center justify-between">
                    <flux:text class="text-xs text-zinc-500">{{ $game->difficulty }}</flux:text>
                    @if ($playUrl = $game->playUrl())
                        <flux:button :href="$playUrl" wire:navigate size="sm" variant="primary" class="font-pixel !text-[0.6rem]">
                            {{ __('Play') }}
                        </flux:button>
                    @else
                        <flux:button size="sm" variant="ghost" disabled class="font-pixel !text-[0.6rem]">
                            {{ __('Soon') }}
                        </flux:button>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full flex flex-col items-center gap-3 rounded-xl border-2 border-dashed border-zinc-700 py-16 text-center">
                <span class="text-5xl opacity-60">🕹️</span>
                <flux:heading class="font-pixel !text-sm text-zinc-300">{{ __('Game Over') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ __('No cartridges match your search.') }}</flux:text>
            </div>
        @endforelse
    </div>
</div>
