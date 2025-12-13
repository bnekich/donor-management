<?php

namespace App\Livewire\Donors;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Donor;
use Livewire\WithFileUploads;
use App\Models\Campaign;

class Edit extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $first_name = '';
    #[Validate('required|string|max:255')]
    public string $last_name = '';
    #[Validate('required|email|max:255')]
    public string $email = '';
    #[Validate('nullable|string|max:20')]
    public ?string $phone_number;
    #[Validate('nullable|date')]
    public ?string $start_date = null;
    #[Validate('nullable|file|max:10240')]
    public $media; // Max 10MB
    #[Validate([
        'selectedCampaigns'   => ['nullable', 'array'],
        'selectedCampaigns.*' => ['exists:campaigns,id'],
    ])]
    public array $selectedCampaigns = [];

    public Donor $donor;

    public function mount(Donor $donor): void
    {
        $this->donor = $donor;
        $this->donor->load('media', 'campaigns');
        $this->first_name = $donor->first_name;
        $this->last_name = $donor->last_name;
        $this->email = $donor->email;
        $this->phone_number = $donor->phone_number;
        $this->start_date = $donor->start_date?->format('Y-m-d');
        $this->selectedCampaigns = $donor->campaigns->pluck('id')->toArray();
    }

    public function save(): void
    {
        $this->validate();

        $this->donor->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'start_date' => $this->start_date,
        ]);

        if ($this->media) {
            $this->donor->getFirstMedia()?->delete();
            $this->donor->addMedia($this->media)->toMediaCollection();
        }

        $this->donor->campaigns()->sync($this->selectedCampaigns ?? []);

        session()->flash('success', 'Donor successfully updated.');

        $this->redirectRoute('donors.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.donors.edit', ['campaigns' => Campaign::where('is_active', true)->orderBy('name')->get()]);
    }
}
