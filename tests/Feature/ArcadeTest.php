<?php

use App\Models\Game;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected away from the arcade', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users see the games grid', function () {
    $game = Game::factory()->create(['title' => 'Dragonspire Saga']);

    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee($game->title);
});

test('featured games are ordered before the rest', function () {
    Game::factory()->create(['title' => 'Zzz Regular', 'is_featured' => false]);
    Game::factory()->featured()->create(['title' => 'Aaa Featured']);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::arcade')
        ->assertSeeInOrder(['Aaa Featured', 'Zzz Regular']);
});

test('the search filters games by title', function () {
    Game::factory()->create(['title' => 'Neon Circuit']);
    Game::factory()->create(['title' => 'Crystal Vault']);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::arcade')
        ->set('search', 'Neon')
        ->assertSee('Neon Circuit')
        ->assertDontSee('Crystal Vault');
});

test('the genre filter narrows the grid', function () {
    Game::factory()->create(['title' => 'Logic One', 'genre' => 'Logic']);
    Game::factory()->create(['title' => 'Math One', 'genre' => 'Math']);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::arcade')
        ->set('genre', 'Logic')
        ->assertSee('Logic One')
        ->assertDontSee('Math One')
        ->call('clearGenre')
        ->assertSet('genre', '')
        ->assertSee('Math One');
});

test('an empty result shows the game over state', function () {
    Game::factory()->create(['title' => 'Neon Circuit']);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::arcade')
        ->set('search', 'nonexistent-cartridge')
        ->assertSee('Game Over');
});
