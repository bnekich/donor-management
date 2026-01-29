<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    public bool $is_admin = false;

    public User $user;

    public bool $showResetPasswordModal = false;

    public ?string $newTemporaryPassword = null;

    public function mount(User $user): void
    {
        $this->authorize('update', $user);
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = $user->is_admin;
    }

    public function save(): void
    {
        $this->authorize('update', $this->user);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
        ]);

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
        ]);

        session()->flash('success', __('User updated successfully.'));

        $this->redirectRoute('users.show', $this->user, navigate: true);
    }

    public function resetPassword(): void
    {
        $this->authorize('update', $this->user);

        $this->newTemporaryPassword = Str::password(12);

        $this->user->update([
            'password' => $this->newTemporaryPassword,
            'password_changed_at' => null,
        ]);

        $this->showResetPasswordModal = true;
    }

    public function closeResetPasswordModal(): void
    {
        $this->showResetPasswordModal = false;
        $this->newTemporaryPassword = null;
        session()->flash('success', __('Temporary password was set. Give it to the user securely.'));
        $this->redirectRoute('users.show', $this->user, navigate: true);
    }

    public function render()
    {
        return view('livewire.users.edit');
    }
}
