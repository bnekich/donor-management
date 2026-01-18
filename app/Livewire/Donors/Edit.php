<?php

namespace App\Livewire\Donors;

use App\Models\Campaign;
use App\Models\Donor;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

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
    public ?string $phone = null;

    #[Validate('nullable|file|max:10240')]
    public $media; // Max 10MB

    #[Validate([
        'selectedCampaigns' => ['nullable', 'array'],
        'selectedCampaigns.*' => ['exists:campaigns,id'],
    ])]
    public array $selectedCampaigns = [];

    public Donor $donor;

    public function mount(Donor $donor): void
    {
        $this->donor = $donor;
        $this->donor->load('donorDetail', 'media', 'campaigns');
        $detail = $donor->donorDetail;
        $this->first_name = $detail?->first_name ?? '';
        $this->last_name = $detail?->last_name ?? '';
        $this->email = $donor->email;
        $this->phone = $donor->phone;
        $this->selectedCampaigns = $donor->campaigns->pluck('id')->toArray();
    }

    public function save(): void
    {
        $this->validate();

        $this->donor->update([
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        if ($this->donor->donorDetail) {
            $this->donor->donorDetail->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]);
        } else {
            $this->donor->donorDetail()->create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]);
        }

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
