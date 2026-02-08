<?php

namespace App\Policies;

use App\Models\Bar;
use App\Models\User;

class BarPolicy
{
    /**
     * Determine if the user can create a bar.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the bar.
     */
    public function view(User $user, Bar $bar): bool
    {
        return $user->is_admin || $bar->canBeAccessedBy($user);
    }

    /**
     * Determine if the user can update the bar.
     */
    public function update(User $user, Bar $bar): bool
    {
        if ($bar->is_demo) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        $pivot = $bar->users()->where('user_id', $user->id)->first()?->pivot;

        return $pivot && in_array($pivot->role, ['owner', 'manager']);
    }

    /**
     * Determine if the user can delete the bar.
     */
    public function delete(User $user, Bar $bar): bool
    {
        if ($bar->is_demo) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        $pivot = $bar->users()->where('user_id', $user->id)->first()?->pivot;

        return $pivot && $pivot->role === 'owner';
    }

    /**
     * Determine if the user can update bar settings (branding).
     */
    public function updateSettings(User $user, Bar $bar): bool
    {
        return $this->update($user, $bar);
    }

    /**
     * Determine if the user can manage beers for the bar.
     */
    public function manageBeers(User $user, Bar $bar): bool
    {
        if ($bar->is_demo) {
            return false;
        }

        return $this->update($user, $bar);
    }

    /**
     * Determine if the user can manage wines for the bar.
     */
    public function manageWines(User $user, Bar $bar): bool
    {
        if ($bar->is_demo) {
            return false;
        }

        return $this->update($user, $bar);
    }
}
