<section class="max-w-5xl">
    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="first_name" :label="__('First Name')" required badge="required" />
        <flux:input wire:model="last_name" :label="__('Last Name')" required badge="required" />
        <flux:input wire:model="email" :label="__('Email')" type="email" required badge="required" />
        <flux:input wire:model="phone_number" :label="__('Phone Number')" required badge="required" />
        <div>
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </div>
    </form>
</section>
