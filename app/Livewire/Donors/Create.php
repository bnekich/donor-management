<?php

namespace App\Livewire\Donors;

use App\Models\Donor;
use Livewire\Component;
use Livewire\Attributes\Validate;
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
    public string $phone_number = '';
    #[Validate('nullable|date')]
    public ?string $start_date = null;
    #[Validate('nullable|file|max:10240')] // Max 10MB
    public $media;

    public function save(): void
    {
        $this->validate();

        $donor = Donor::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'start_date' => $this->start_date,
        ]);

        if ($this->media) {
            $donor->addMedia($this->media)->toMediaCollection();
        }

        session()->flash('success', 'Donor successfully added.');

        $this->redirectRoute('donors.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.donors.create');
    }
}
