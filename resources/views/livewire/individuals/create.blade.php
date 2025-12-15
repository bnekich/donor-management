<section class="max-w-5xl">
    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="first_name" :label="__('First Name')" required badge="required" />
        <flux:input wire:model="last_name" :label="__('Last Name')" required badge="required" />
        <flux:input wire:model="email" type="email" :label="__('Email')" required badge="required" />
        <flux:input wire:model="phone" :label="__('Phone Number')" required badge="required" />
        <flux:input wire:model='address_line1' :label="__('Address')" required badge="required" />
        <flux:input wire:model='address_line2' :label="__('')" />
        <flux:input wire:model='city' :label="__('City')" required badge="required" />
        <flux:input wire:model='county' :label="__('County')" />
        <flux:input wire:model='state' :label="__('State/Province')" required badge="required" />
        <flux:input wire:model='zip' :label="__('Postal Code')" required badge="required" />
        <flux:input wire:model='country' :label="__('Country')" required badge="required" />
        <flux:input wire:model='birthday' :label="__('Birthday')" type="date" />
        <flux:input wire:model='occupation' :label="__('Occupation')" />
        <flux:select wire:model="selectedOrganization" :label="__('Organization')">
            <option value="">{{ __('Select an Organization') }}</option>
            @foreach ($organizations as $organization)
                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
            @endforeach
        </flux:select>




        <div>
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </div>
    </form>
</section>
