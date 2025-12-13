<section class="max-w-5xl">
    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="name" :label="__('Name')" required badge="required" />
        <flux:textarea wire:model="description" :label="__('Description')" />
        <flux:input wire:model="start_date" type="date" :label="__('Start Date')" />
        <flux:input wire:model="end_date" type="date" :label="__('End Date')" />
        <flux:input wire:model="goal_amount" type="number" step="0.01" :label="__('Goal Amount')" />
        <flux:checkbox wire:model="is_active" :label="__('Is Active')" />

        <div>
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </div>
    </form>
</section>
