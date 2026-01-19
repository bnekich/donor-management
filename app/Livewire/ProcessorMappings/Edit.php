<?php

namespace App\Livewire\ProcessorMappings;

use App\Models\ProcessorMapping;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public ProcessorMapping $mapping;

    #[Validate('required|string|max:255')]
    public string $processor = '';

    #[Validate('required|string|max:255')]
    public string $source_field = '';

    #[Validate('required|string|max:255')]
    public string $target_field = '';

    #[Validate('required|string|in:direct,callback,lookup,computed')]
    public string $transformation_type = 'direct';

    public ?array $transformation_config = null;

    #[Validate('boolean')]
    public bool $is_required = false;

    #[Validate('required|integer|min:0')]
    public int $priority = 0;

    #[Validate('boolean')]
    public bool $is_active = true;

    public function mount(ProcessorMapping $processorMapping): void
    {
        $this->mapping = $processorMapping;
        $this->processor = $processorMapping->processor;
        $this->source_field = $processorMapping->source_field;
        $this->target_field = $processorMapping->target_field;
        $this->transformation_type = $processorMapping->transformation_type;
        $this->transformation_config = $processorMapping->transformation_config;
        $this->is_required = $processorMapping->is_required;
        $this->priority = $processorMapping->priority;
        $this->is_active = $processorMapping->is_active;
    }

    public function save(): void
    {
        $this->validate();

        $this->mapping->update([
            'processor' => $this->processor,
            'source_field' => $this->source_field,
            'target_field' => $this->target_field,
            'transformation_type' => $this->transformation_type,
            'transformation_config' => $this->transformation_config,
            'is_required' => $this->is_required,
            'priority' => $this->priority,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Processor mapping updated successfully.');

        $this->redirectRoute('processor-mappings.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.processor-mappings.edit');
    }
}
