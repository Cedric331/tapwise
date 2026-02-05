<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Bar;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'bar_name' => ['required', 'string', 'max:255'],
            'bar_slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:bars,slug'],
        ], [
            'name.required' => 'Le nom complet est obligatoire.',
            'name.string' => 'Le nom complet doit être une chaîne de caractères.',
            'name.max' => 'Le nom complet ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur, veuillez en choisir une autre.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'bar_name.required' => 'Le nom du bar est obligatoire.',
            'bar_slug.required' => 'L\'identifiant unique (slug) est obligatoire.',
            'bar_slug.regex' => 'Le slug ne peut contenir que des lettres minuscules, chiffres et tirets.',
            'bar_slug.unique' => 'Ce slug est déjà utilisé par un autre bar, veuillez le modifier.',
        ])->validate();
        

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'trial_ends_at' => now()->addDays(14),
            ]);

            // Create bar for the user
            $bar = Bar::create([
                'name' => $input['bar_name'],
                'slug' => Str::slug($input['bar_slug']),
                'qr_enabled' => true,
                'is_demo' => false,
            ]);

            // Attach user to bar as owner
            $bar->users()->attach($user->id, ['role' => 'owner']);

            return $user;
        });
    }
}
