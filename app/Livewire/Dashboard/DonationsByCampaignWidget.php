<?php

namespace App\Livewire\Dashboard;

use App\Models\Donation;
use Livewire\Component;

class DonationsByCampaignWidget extends Component
{
    public $count;

    public $totalAmount;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $donations = Donation::query()->get();

        $this->count = $donations->count();
        $this->totalAmount = $donations->sum('amount');
    }

    public function render()
    {
        return view('livewire.dashboard.donations-by-campaign-widget');
    }
}
