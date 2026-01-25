<div class="flex flex-col gap-6">
    <x-auth-header 
        :title="__('Change Your Password')" 
        :description="__('You must change your password before continuing. Please enter a new password below.')" 
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="changePassword" class="flex flex-col gap-6">
        <!-- Password -->
        <flux:input
            wire:model="password"
            name="password"
            :label="__('New Password')"
            type="password"
            required
            autofocus
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            name="password_confirmation"
            :label="__('Confirm Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Change Password') }}</span>
                <span wire:loading>{{ __('Changing Password...') }}</span>
            </flux:button>
        </div>
    </form>
</div>
