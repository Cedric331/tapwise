<?php

namespace App\Models;

use App\Enums\WineColor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Wine extends Model
{
    use HasFactory;

    protected $fillable = [
        'bar_id',
        'name',
        'color',
        'grape',
        'region',
        'food_pairings',
        'abv_x10',
        'description',
        'is_available',
        'price',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'color' => WineColor::class,
        'food_pairings' => 'array',
    ];

    /**
     * Get the bar that owns this wine.
     */
    public function bar(): BelongsTo
    {
        return $this->belongsTo(Bar::class);
    }

    /**
     * Get the tags for this wine.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(WineTag::class, 'wine_wine_tag');
    }

    /**
     * Get the ABV as a decimal (e.g., 12.5 for 125).
     */
    public function getAbvAttribute(): float
    {
        return $this->abv_x10 / 10;
    }

    /**
     * Get the price in euros.
     */
    public function getPriceInEurosAttribute(): ?float
    {
        return $this->price ? $this->price / 100 : null;
    }
}
