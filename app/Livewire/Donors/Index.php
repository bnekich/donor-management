<?php

namespace App\Livewire\Donors;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Donor;

class Index extends Component
{
    use WithPagination;

    public function delete(int $id)
    {
        Donor::where('id', $id)->delete();
        session()->flash('success', 'Donor deleted successfully.');
    }

    public function render()
    {
        return view('livewire.donors.index', ['donors' => Donor::paginate(5)]);
    }
}
