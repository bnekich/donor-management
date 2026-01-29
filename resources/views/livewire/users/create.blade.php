<section class="max-w-5xl">
    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="name" :label="__('Name')" required badge="required" />
        <flux:input wire:model="email" :label="__('Email')" type="email" required badge="required" />
        <flux:checkbox wire:model="is_admin" :label="__('Administrator (can manage users)')" />
        <flux:text variant="subtle">
            {{ __('A temporary password will be generated. The user must change it on first login.') }}
        </flux:text>
        <div>
            <flux:button variant="primary" type="submit">{{ __('Create User') }}</flux:button>
        </div>
    </form>
</section>
