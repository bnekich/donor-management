<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth.simple')]
class ChangePassword extends Component
{
    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Change the password for the currently authenticated user (first-time password change).
     */
    public function changePassword()
    {
        try {
            $validated = $this->validate([
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('password', 'password_confirmation');

            throw $e;
        }

        $user = Auth::user();
        $user->update([
            'password' => $validated['password'],
        ]);
        $user->markPasswordAsChanged();

        $this->reset('password', 'password_confirmation');

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }
}
