<section class="max-w-5xl">
    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="processor" :label="__('Processor')" required badge="required" placeholder="e.g., givebutter, stripe" />
        <flux:input wire:model="source_field" :label="__('Source Field')" required badge="required" placeholder="e.g., data.amount, id" />
        <flux:input wire:model="target_field" :label="__('Target Field')" required badge="required" placeholder="e.g., amount, processor_id" />
        
        <flux:select wire:model="transformation_type" :label="__('Transformation Type')" required badge="required">
            <option value="direct">Direct</option>
            <option value="callback">Callback</option>
            <option value="lookup">Lookup</option>
            <option value="computed">Computed</option>
        </flux:select>

        <flux:input wire:model="priority" :label="__('Priority')" type="number" required badge="required" placeholder="0" />

        <div class="flex gap-6">
            <flux:checkbox wire:model="is_required" :label="__('Required')" />
            <flux:checkbox wire:model="is_active" :label="__('Active')" />
        </div>

        <div>
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            <flux:button href="{{ route('processor-mappings.index') }}" variant="ghost" type="button">{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</section>
