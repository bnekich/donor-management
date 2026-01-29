<section class="max-w-5xl">
    <x-alerts.success />

    <div class="flex gap-4 mb-6">
        <flux:button href="{{ route('users.index') }}" variant="ghost" size="sm">{{ __('Back to Users') }}</flux:button>
        <flux:button href="{{ route('users.edit', $user) }}" variant="filled" size="sm">{{ __('Edit') }}</flux:button>
    </div>

    <div class="space-y-4 rounded-base border border-default bg-neutral-primary-soft p-6">
        <div>
            <flux:subheading>{{ __('Name') }}</flux:subheading>
            <flux:text>{{ $user->name }}</flux:text>
        </div>
        <div>
            <flux:subheading>{{ __('Email') }}</flux:subheading>
            <flux:text>{{ $user->email }}</flux:text>
        </div>
        <div>
            <flux:subheading>{{ __('Admin') }}</flux:subheading>
            <flux:text>{{ $user->is_admin ? __('Yes') : __('No') }}</flux:text>
        </div>
        <div>
            <flux:subheading>{{ __('Created') }}</flux:subheading>
            <flux:text>{{ $user->created_at?->format('M j, Y g:i A') }}</flux:text>
        </div>
        <div>
            <flux:subheading>{{ __('Password changed') }}</flux:subheading>
            <flux:text>{{ $user->password_changed_at ? $user->password_changed_at->format('M j, Y') : __('Never (must change on first login)') }}</flux:text>
        </div>
    </div>
</section>
