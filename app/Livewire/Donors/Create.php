<?php

namespace App\Livewire\Donors;

use App\Models\Donor;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $first_name = '';
    #[Validate('required|string|max:255')]
    public string $last_name = '';
    #[Validate('required|email|max:255')]
    public string $email = '';
    #[Validate('required|string|max:20')]
    public string $phone_number = '';

    public function save(): void
    {
        $this->validate();

        Donor::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
        ]);

        session()->flash('success', 'Donor successfully added.');

        $this->redirectRoute('donors.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.donors.create');
    }
}
