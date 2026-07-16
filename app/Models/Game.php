<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $genre
 * @property string $description
 * @property string $sprite
 * @property string $accent_color
 * @property string $difficulty
 * @property bool $is_featured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['title', 'slug', 'genre', 'description', 'sprite', 'accent_color', 'difficulty', 'is_featured'])]
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    /**
     * The route to the playable component for this game, or null if it is not yet playable.
     */
    public function playUrl(): ?string
    {
        return match ($this->slug) {
            'equation-quest' => route('games.equation-quest'),
            default => null,
        };
    }

    /**
     * Scope a query to featured games first, then alphabetically.
     *
     * @param  Builder<Game>  $query
     */
    public function scopeArcadeOrder(Builder $query): void
    {
        $query->orderByDesc('is_featured')->orderBy('title');
    }

    /**
     * Scope a query to games matching a free-text search on title or genre.
     *
     * @param  Builder<Game>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $query->when(filled($term), function (Builder $query) use ($term): void {
            $query->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('genre', 'like', "%{$term}%");
            });
        });
    }
}
