<?php

namespace App\Livewire\Campaigns;

use Livewire\Component;
use Illuminate\View\View;
use App\Models\Campaign;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function delete(int $id): void
    {
        $campaign = Campaign::findOrFail($id);

        if ($campaign->donors()->count() > 0) {
            $campaign->donors()->detach();
        }

        $campaign->delete();
    }

    public function render(): View
    {
        return view('livewire.campaigns.index', [
            'campaigns' => Campaign::paginate(10),
        ]);
    }
}
