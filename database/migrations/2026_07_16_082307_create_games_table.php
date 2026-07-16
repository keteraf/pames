<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('genre')->index();
            $table->text('description');
            $table->string('sprite', 16)->comment('Emoji used as the retro cover sprite.');
            $table->string('accent_color', 7)->default('#7c3aed')->comment('Hex color driving the card glow.');
            $table->string('difficulty')->default('Medium');
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
