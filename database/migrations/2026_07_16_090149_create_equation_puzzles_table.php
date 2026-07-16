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
        Schema::create('equation_puzzles', function (Blueprint $table) {
            $table->id();
            $table->date('puzzle_date')->unique()->comment('The day this daily puzzle belongs to.');
            $table->json('numbers')->comment('The six source numbers dealt to the player.');
            $table->unsignedSmallInteger('target')->comment('The number the player must reach.');
            $table->string('solution')->comment('A known solving expression, used for the reveal.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equation_puzzles');
    }
};
