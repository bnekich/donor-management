<section>
    <x-alerts.success />

    @session('error')
        <flux:callout variant="danger" icon="x-circle" class="mb-4" heading="{{ $value }}" />
    @endsession

    @if (session('temporary_password'))
        <flux:callout variant="warning" icon="key" class="mb-4" heading="{{ __('New user created') }}">
            <p class="mb-2">{{ __('Give this temporary password to the user securely. They must change it on first login.') }}</p>
            <p class="font-mono font-semibold break-all">{{ session('temporary_password') }}</p>
            <p class="mt-2 text-sm opacity-90">{{ __('User:') }} {{ session('new_user_name') }} ({{ session('new_user_email') }})</p>
        </flux:callout>
    @endif

    <div class="flex flex-grow gap-x-4 mb-4">
        <flux:button href="{{ route('users.create') }}" variant="filled">{{ __('Add User') }}</flux:button>
    </div>

    <div class="mb-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search by name or email...') }}"
            icon="magnifying-glass"
        />
    </div>

    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Name') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Email') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Admin') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Created') }}</th>
                    <th scope="col" class="px-6 py-3 font-medium">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b hover:bg-neutral-secondary-soft/50" wire:key="user-{{ $user->id }}">
                        <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                        <td class="px-6 py-4 font-medium">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if ($user->is_admin)
                                <flux:badge color="blue">{{ __('Yes') }}</flux:badge>
                            @else
                                <flux:badge variant="outline">{{ __('No') }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $user->created_at?->format('M j, Y') }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <flux:button href="{{ route('users.show', $user) }}" variant="ghost" size="sm">
                                {{ __('View') }}
                            </flux:button>
                            <flux:button href="{{ route('users.edit', $user) }}" variant="filled" size="sm">
                                {{ __('Edit') }}
                            </flux:button>
                            @if ($user->id !== auth()->id())
                                <flux:button
                                    wire:confirm="{{ __('Are you sure you want to delete this user?') }}"
                                    wire:click="delete({{ $user->id }})"
                                    variant="danger"
                                    size="sm"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($users->hasPages())
            <div class="p-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</section>
