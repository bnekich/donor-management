<section class="max-w-5xl">
    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="first_name" :label="__('First Name')" required badge="required" />
        <flux:input wire:model="last_name" :label="__('Last Name')" required badge="required" />
        <flux:input wire:model="email" :label="__('Email')" type="email" required badge="required" />
        <flux:input wire:model="phone_number" :label="__('Phone Number')" />
        <flux:input wire:model="start_date" :label="__('Start Date')" type="date" />
        <flux:input wire:model='media' :label="__('Media')" type="file" />
        @if ($donor->media_file)
            <div>
                <a href="{{ $donor->media_file->original_url }}" target="_blank">
                    <img src="{{ $donor->media_file->original_url }}" alt="{{ $donor->last_name }}" class="w-32 h-32" />
                </a>
            </div>
        @endif
        <flux:label>
            Campaigns
        </flux:label>

        <ul
            class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            @foreach ($campaigns as $campaign)
                <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                    <div class="flex items-center ps-3">
                        <input wire:model="selectedCampaigns" id="{{ $campaign->id }}-checkbox-list" type="checkbox"
                            value="{{ $campaign->id }}"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <label for="{{ $campaign->id }}-checkbox-list"
                            class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $campaign->name }}</label>
                    </div>
                </li>
            @endforeach
        </ul>

        <div>
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </div>
    </form>
</section>
