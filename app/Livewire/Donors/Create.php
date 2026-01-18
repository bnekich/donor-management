<?php

namespace App\Livewire\Donors;

use App\Models\Campaign;
use App\Models\Donor;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|file|max:10240')] // Max 10MB
    public $media;

    public function save(): void
    {
        $this->validate();

        $donor = Donor::create([
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        $donor->donorDetail()->create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ]);

        if ($this->media) {
            $donor->addMedia($this->media)->toMediaCollection();
        }

        $donor->campaigns()->sync($this->selectedCampaigns ?? []);

        session()->flash('success', 'Donor successfully added.');

        $this->redirectRoute('donors.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.donors.create', ['campaigns' => Campaign::where('is_active', true)->orderBy('name')->get()]);
    }
}
