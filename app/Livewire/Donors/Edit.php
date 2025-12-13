<?php

namespace App\Livewire\Donors;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Donor;

class Edit extends Component
{
    #[Validate('required|string|max:255')]
    public string $first_name = '';
    #[Validate('required|string|max:255')]
    public string $last_name = '';
    #[Validate('required|email|max:255')]
    public string $email = '';
    #[Validate('required|string|max:20')]
    public string $phone_number = '';

    public Donor $donor;

    public function mount(Donor $donor): void
    {
        $this->donor = $donor;
        $this->first_name = $donor->first_name;
        $this->last_name = $donor->last_name;
        $this->email = $donor->email;
        $this->phone_number = $donor->phone_number;
    }

    public function save(): void
    {
        $this->validate();

        $this->donor->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
        ]);

        session()->flash('success', 'Donor successfully updated.');

        $this->redirectRoute('donors.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.donors.edit');
    }
}
