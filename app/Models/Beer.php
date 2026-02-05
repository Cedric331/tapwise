<?php

namespace App\Models;

use App\Enums\BeerColor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Beer extends Model
{
    use HasFactory;

    protected $fillable = [
        'bar_id',
        'name',
        'brewery',
        'style',
        'color',
        'abv_x10',
        'ibu',
        'description',
        'is_on_tap',
        'is_available',
        'price',
    ];

    protected $casts = [
        'is_on_tap' => 'boolean',
        'is_available' => 'boolean',
        'color' => BeerColor::class,
    ];

    /**
     * Get the bar that owns this beer.
     */
    public function bar(): BelongsTo
    {
        return $this->belongsTo(Bar::class);
    }

    /**
     * Get the tags for this beer.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the ABV as a decimal (e.g., 4.5 for 45).
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

