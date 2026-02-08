<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarScanEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'bar_id',
    ];

    /**
     * Get the bar that owns the scan event.
     */
    public function bar(): BelongsTo
    {
        return $this->belongsTo(Bar::class);
    }
}

