<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::arcade')->name('dashboard');
    Route::livewire('games/equation-quest', 'pages::equation-quest')->name('games.equation-quest');
});

require __DIR__.'/settings.php';
