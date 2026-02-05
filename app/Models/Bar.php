<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'brand_background_color',
        'brand_primary_color',
        'welcome_message',
        'qr_enabled',
        'count_scans',
        'is_demo',
    ];

    protected $casts = [
        'qr_enabled' => 'boolean',
        'count_scans' => 'integer',
        'is_demo' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the users that belong to this bar.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the beers for this bar.
     */
    public function beers(): HasMany
    {
        return $this->hasMany(Beer::class);
    }

    /**
     * Get the available beers for this bar.
     */
    public function availableBeers(): HasMany
    {
        return $this->beers()->where('is_available', true);
    }

    /**
     * Check if a user can access this bar.
     */
    public function canBeAccessedBy(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the public URL for this bar.
     */
    public function getPublicUrlAttribute(): string
    {
        return url("/b/{$this->slug}");
    }
}

