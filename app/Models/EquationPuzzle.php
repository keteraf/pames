<?php

namespace App\Models;

use Database\Factories\EquationPuzzleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $puzzle_date
 * @property list<int> $numbers
 * @property int $target
 * @property string $solution
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['puzzle_date', 'numbers', 'target', 'solution'])]
class EquationPuzzle extends Model
{
    /** @use HasFactory<EquationPuzzleFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'puzzle_date' => 'date',
            'numbers' => 'array',
            'target' => 'integer',
        ];
    }
}
