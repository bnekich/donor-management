<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    #[Validate(rule: 'required|string|max:255')]
    public ?string $name = null;
    #[Validate(rule: 'nullable|string')]
    public ?string $description = null;
    #[Validate(rule: 'required|date')]
    public ?string $start_date = null;
    #[Validate(rule: 'nullable|date')]
    public ?string $end_date = null;
    #[Validate(rule: 'required|numeric|min:0')]
    public ?float $goal_amount = null;
    #[Validate(rule: 'boolean')]
    public ?bool $is_active = true;

    public Campaign $campaign;

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;
        $this->name = $campaign->name;
        $this->description = $campaign->description;
        $this->start_date = $campaign->start_date;
        $this->end_date = $campaign->end_date;
        $this->goal_amount = $campaign->goal_amount;
        $this->is_active = $campaign->is_active;
    }

    public function save(): void
    {
        $this->validate();

        $this->campaign->update([
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'goal_amount' => $this->goal_amount,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Campaign updated successfully.');

        $this->redirectRoute('campaigns.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.campaigns.edit');
    }
}
