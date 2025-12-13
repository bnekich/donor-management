<?php

namespace App\Livewire\Campaigns;

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Campaign;

class Create extends Component
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

    public function save()
    {
        $this->validate();

        Campaign::create([
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'goal_amount' => $this->goal_amount,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Campaign created successfully.');

        $this->redirectRoute('campaigns.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.campaigns.create');
    }
}
