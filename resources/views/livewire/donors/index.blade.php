<section>
    <x-alerts.success />

    <div class="flex flex-grow gap-x-4 mb-4">
        <flux:button href="{{ route('donors.create') }}" variant="filled">{{ __('Add Donor') }}</flux:button>
    </div>
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        First Name
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Last Name
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Phone Number
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Start Date
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        File
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($donors as $donor)
                    <tr class="border-b hover:bg-neutral-secondary-soft/50">
                        <td class="px-6 py-4 font-medium">
                            {{ $donor->first_name }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $donor->last_name }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $donor->email }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $donor->phone_number }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $donor->start_date?->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($donor->media_file)
                                <a href="{{ $donor->media_file->original_url }}" target="_blank">
                                    <img src="{{ $donor->media_file->original_url }}" alt="{{ $donor->last_name }}"
                                        class="w-8 h-8" />
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 space-x=2">
                            <flux:button href="{{ route('donors.edit', $donor) }}" variant="filled">
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button wire:confirm="{{ __('Are you sure you want to delete this donor?') }}"
                                wire:click="delete({{ $donor->id }})" variant="danger" type="button">
                                {{ __('Delete') }}</flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($donors->hasPages())
            <div class="p-4">
                {{ $donors->links() }}
            </div>
        @endif
    </div>

</section>
