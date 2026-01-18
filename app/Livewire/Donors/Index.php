<?php

namespace App\Livewire\Donors;

use App\Models\Donor;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'campaigns', except: '')]
    public ?array $selectedCampaigns = [];

    public function delete(int $id)
    {
        Donor::where('id', $id)->delete();
        session()->flash('success', 'Donor deleted successfully.');
    }

    public function render()
    {
        return view('livewire.donors.index', [

            'donors' => Donor::query()
                ->with('donorDetail', 'organization', 'media', 'campaigns')
                ->when($this->selectedCampaigns, function (Builder $query) {
                    $query->whereHas('campaigns', function (Builder $query) {
                        $query->whereIn('campaigns.id', $this->selectedCampaigns);
                    });
                })
                ->with('donorDetail')
                ->orderBy('email')
                ->paginate(5),

            'campaigns' => \App\Models\Campaign::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
