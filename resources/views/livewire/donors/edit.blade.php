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
        <div>
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </div>
    </form>
</section>
