<?php

namespace App\Livewire\Individuals;

use Livewire\Component;
use App\Models\Individual;
use Livewire\WithPagination;

class Index extends Component
{
    public function delete(int $id): void
    {
        $individual = Individual::findOrFail($id);
        $individual->delete();
    }

    public function render()
    {
        return view('livewire.individuals.index', [
            'individuals' => Individual::paginate(10),
        ]);
    }
}
