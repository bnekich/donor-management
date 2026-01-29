<section class="max-w-5xl">
    <x-alerts.success />

    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="name" :label="__('Name')" required badge="required" />
        <flux:input wire:model="email" :label="__('Email')" type="email" required badge="required" />
        <flux:checkbox wire:model="is_admin" :label="__('Administrator (can manage users)')" />
        <div class="flex gap-4">
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            <flux:button href="{{ route('users.show', $user) }}" variant="ghost">{{ __('Cancel') }}</flux:button>
        </div>
    </form>

    <flux:separator class="my-8" />

    <div>
        <flux:heading size="md">{{ __('Reset password') }}</flux:heading>
        <flux:text variant="subtle" class="mb-4">
            {{ __('Set a new temporary password. The user will be required to change it on next login.') }}
        </flux:text>
        <flux:button variant="outline" wire:click="resetPassword">
            {{ __('Reset password') }}
        </flux:button>
    </div>

    <flux:modal name="reset-password-modal" class="max-w-md" wire:model="showResetPasswordModal" @close="closeResetPasswordModal">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Temporary password set') }}</flux:heading>
            <flux:callout variant="warning" icon="key">
                <p class="mb-2">{{ __('Give this temporary password to the user securely. They must change it on next login.') }}</p>
                @if ($newTemporaryPassword)
                    <p class="font-mono font-semibold break-all">{{ $newTemporaryPassword }}</p>
                @endif
            </flux:callout>
            <flux:button variant="primary" wire:click="closeResetPasswordModal" class="w-full">
                {{ __('Close') }}
            </flux:button>
        </div>
    </flux:modal>
</section>
