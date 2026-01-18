<?php

namespace App\Livewire\Individuals;

use App\Models\DonorDetail;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $id): void
    {
        $donorDetail = DonorDetail::findOrFail($id);
        $donorDetail->delete();
        session()->flash('success', 'Individual donor successfully deleted.');
    }

    public function render()
    {
        return view('livewire.individuals.index', [
            'individuals' => DonorDetail::with('donor', 'organization')
                ->orderBy('last_name', 'asc')
                ->paginate(10),
        ]);
    }
}
