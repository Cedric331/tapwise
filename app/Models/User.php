<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Cashier\Billable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_admin' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (User $user) {
            try {
                if ($user->hasStripeId() && $user->subscribed('default')) {
                    $user->subscription('default')->cancelNow();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur lors de l\'annulation de l\'abonnement Stripe lors de la suppression du compte', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

        /**
     * Check if the user can access the admin panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    /**
     * Get the bars that belong to this user.
     */
    public function bars(): BelongsToMany
    {
        return $this->belongsToMany(Bar::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function hasActiveTrial(): bool
    {
        return $this->trial_ends_at !== null && $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(string $subscriptionName): bool
    {
        return $this->subscriptions()
            ->where('type', $subscriptionName)
            ->active()
            ->exists();
    }

    public function hasAnyActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->active()
            ->exists();
    }
}
