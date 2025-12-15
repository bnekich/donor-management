<section>
    <x-alerts.success />

    <div class="flex flex-grow gap-x-4 mb-4">
        <flux:button href="{{ route('individuals.create') }}" variant="filled">{{ __('Add Individual Donor') }}
        </flux:button>
    </div>

    {{-- <div class="mb-4 flex flex-row justify-end gap-x-2 w-full">
        <ul
            class="items-center w-fit text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            @foreach ($campaigns as $campaign)
                <li class="w-full pr-2 border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                    <div class="flex items-center ps-3">
                        <input wire:model.live="selectedCampaigns" id="{{ $campaign->id }}-checkbox-list" type="checkbox"
                            value="{{ $campaign->id }}"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <label for="{{ $campaign->id }}-checkbox-list"
                            class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $campaign->name }}</label>
                    </div>
                </li>
            @endforeach
        </ul>
    </div> --}}

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
                    {{-- <th scope="col" class="px-6 py-3 font-medium">
                        File
                    </th> --}}
                    {{-- <th scope="col" class="px-6 py-3 font-medium">
                        Campaigns
                    </th> --}}
                    <th scope="col" class="px-6 py-3 font-medium">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($individuals as $individual)
                    <tr class="border-b hover:bg-neutral-secondary-soft/50">
                        <td class="px-6 py-4 font-medium">
                            {{ $individual->first_name }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $individual->last_name }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $individual->email }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            {{ $individual->phone }}
                        </td>
                        {{-- <td class="px-6 py-4">
                            @if ($donor->media_file)
                                <a href="{{ $donor->media_file->original_url }}" target="_blank">
                                    <img src="{{ $donor->media_file->original_url }}" alt="{{ $donor->last_name }}"
                                        class="w-8 h-8" />
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            @foreach ($donor->campaigns as $campaign)
                                <flux:badge>{{ $campaign->name }}</flux:badge>
                            @endforeach
                        </td> --}}
                        <td class="px-6 py-4 space-x=2">
                            {{-- <flux:button href="{{ route('individuals.edit', $individual) }}" variant="filled">
                                {{ __('Edit') }}
                            </flux:button> --}}
                            <flux:button href="#" variant="filled">
                                {{ __('Edit') }}
                            </flux:button>

                            <flux:button wire:confirm="{{ __('Are you sure you want to delete this donor?') }}"
                                wire:click="delete({{ $individual->id }})" variant="danger" type="button">
                                {{ __('Delete') }}</flux:button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($individuals->hasPages())
            <div class="p-4">
                {{ $individuals->links() }}
            </div>
        @endif
    </div>

</section>
