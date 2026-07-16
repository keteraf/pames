<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100 antialiased">
        <div class="scanlines relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-6 py-12">
            {{-- Ambient glow + drifting sprites --}}
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_20%,rgba(124,58,237,0.25),transparent_60%)]"></div>
            <div class="pointer-events-none absolute left-8 top-16 text-6xl opacity-20 select-none">🧮</div>
            <div class="pointer-events-none absolute bottom-16 right-10 text-6xl opacity-20 select-none">🧩</div>
            <div class="pointer-events-none absolute bottom-24 left-1/4 text-5xl opacity-10 select-none">🔢</div>

            <main class="relative z-10 flex w-full max-w-xl flex-col items-center text-center">
                <flux:heading class="font-pixel !text-3xl leading-relaxed text-violet-300 drop-shadow-[0_3px_0_rgba(0,0,0,0.9)] sm:!text-5xl">
                    PAMES
                </flux:heading>

                <flux:text class="mt-6 max-w-md text-zinc-400">
                    {{ __('A cabinet full of logic and math puzzles. Log in, pick a game, and train your brain.') }}
                </flux:text>

                <div class="mt-10 flex flex-col items-center gap-4 sm:flex-row">
                    @auth
                        <flux:button :href="route('dashboard')" variant="primary" class="font-pixel !text-xs" wire:navigate>
                            {{ __('Enter Arcade') }}
                        </flux:button>
                    @else
                        <flux:button :href="route('login')" variant="primary" class="font-pixel !text-xs" wire:navigate>
                            {{ __('Press Start') }}
                        </flux:button>

                        @if (Route::has('register'))
                            <flux:button :href="route('register')" variant="ghost" class="font-pixel !text-xs" wire:navigate>
                                {{ __('New Player') }}
                            </flux:button>
                        @endif
                    @endauth
                </div>

                <flux:text class="font-pixel mt-14 animate-pulse !text-[0.6rem] text-zinc-600">
                    {{ __('© '.date('Y').' — Insert Coin') }}
                </flux:text>
            </main>
        </div>

        @fluxScripts
    </body>
</html>
