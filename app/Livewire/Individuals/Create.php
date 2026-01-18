<?php

namespace App\Livewire\Individuals;

use App\Models\Donor;
use App\Models\DonorDetail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('required|string|max:255')]
    public string $last_name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|string|max:255')]
    public string $address_line1 = '';

    #[Validate('nullable|string|max:255')]
    public string $address_line2 = '';

    #[Validate('nullable|string|max:100')]
    public string $city = '';

    #[Validate('nullable|string|max:100')]
    public string $county = '';

    #[Validate('nullable|string|max:100')]
    public string $state = '';

    #[Validate('nullable|string|max:20')]
    public string $zip = '';

    #[Validate('nullable|string|max:100')]
    public string $country = '';

    #[Validate('nullable|date')]
    public ?string $birthday = null;

    #[Validate('nullable|string|max:255')]
    public string $occupation = '';

    #[Validate('nullable|exists:organizations,id')]
    public ?int $selectedOrganization = null;

    public function save(): void
    {
        $this->validate();

        $donor = Donor::create([
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'address_line1' => $this->address_line1 ?: null,
            'address_line2' => $this->address_line2 ?: null,
            'city' => $this->city ?: null,
            'county' => $this->county ?: null,
            'state' => $this->state ?: null,
            'zip' => $this->zip ?: null,
            'country' => $this->country ?: 'USA',
        ]);

        DonorDetail::create([
            'donor_id' => $donor->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birthday' => $this->birthday,
            'occupation' => $this->occupation ?: null,
            'organization_id' => $this->selectedOrganization ?: null,
        ]);

        session()->flash('success', 'Individual donor successfully added.');

        $this->redirectRoute('individuals.index', navigate: true);
    }

    public function render()
    {
        $organizations = \App\Models\Organization::orderBy('name')->get();

        return view('livewire.individuals.create', ['organizations' => $organizations]);
    }
}
