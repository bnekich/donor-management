<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    public bool $is_admin = false;

    public function save(): void
    {
        $this->authorize('create', User::class);

        $this->validate();

        $temporaryPassword = Str::password(12);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $temporaryPassword,
            'password_changed_at' => null,
            'email_verified_at' => now(),
            'is_admin' => $this->is_admin,
        ]);

        session()->flash('success', __('User created successfully.'));
        session()->flash('temporary_password', $temporaryPassword);
        session()->flash('new_user_name', $user->name);
        session()->flash('new_user_email', $user->email);

        $this->redirectRoute('users.index', navigate: true);
    }

    public function render()
    {
        $this->authorize('create', User::class);

        return view('livewire.users.create');
    }
}
