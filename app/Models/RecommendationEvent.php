<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'bar_id',
        'drink_type',
        'item_ids',
    ];

    protected $casts = [
        'item_ids' => 'array',
    ];

    /**
     * Get the bar that owns the recommendation event.
     */
    public function bar(): BelongsTo
    {
        return $this->belongsTo(Bar::class);
    }
}

