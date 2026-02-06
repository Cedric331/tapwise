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
        'recommendation_questions',
        'qr_enabled',
        'count_scans',
        'is_demo',
    ];

    protected $casts = [
        'qr_enabled' => 'boolean',
        'count_scans' => 'integer',
        'is_demo' => 'boolean',
        'recommendation_questions' => 'array',
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

    public function billingUser(): ?User
    {
        $owner = $this->users()->wherePivot('role', 'owner')->first();

        return $owner ?: $this->users()->first();
    }

    public function subscriptionName(): string
    {
        return "bar-{$this->id}";
    }

    public function hasActiveSubscription(): bool
    {
        $billingUser = $this->billingUser();
        if (! $billingUser) {
            return false;
        }

        return $billingUser->hasActiveSubscription($this->subscriptionName());
    }

    public function hasActiveAccess(): bool
    {
        if ($this->is_demo) {
            return true;
        }

        if ($this->hasActiveSubscription()) {
            return true;
        }

        $billingUser = $this->billingUser();
        if (! $billingUser) {
            return false;
        }

        return $billingUser->hasActiveTrial();
    }

    public function subscriptionStatus(): string
    {
        if ($this->is_demo) {
            return 'active';
        }

        if ($this->hasActiveSubscription()) {
            return 'active';
        }

        $billingUser = $this->billingUser();
        if ($billingUser && $billingUser->hasActiveTrial()) {
            return 'trial';
        }

        return 'inactive';
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
