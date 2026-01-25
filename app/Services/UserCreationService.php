<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UserCreationService
{
    /**
     * Create a new user with a temporary password.
     *
     * @param  array<string, mixed>  $attributes
     * @return array{user: User, temporary_password: string}
     */
    public function createUserWithTemporaryPassword(array $attributes): array
    {
        $temporaryPassword = Str::password(16);

        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $temporaryPassword,
            'password_changed_at' => null, // Force password change on first login
        ]);

        return [
            'user' => $user,
            'temporary_password' => $temporaryPassword,
        ];
    }
}
