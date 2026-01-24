<?php

namespace App\Livewire\Dashboard;

use App\Models\Donation;
use Livewire\Component;

class DonationsByCampaignChart extends Component
{
    public $chartData;

    public function mount(): void
    {
        $this->loadData();
        $this->dispatch('render-donations-chart');
    }

    public function loadData(): void
    {
        $donations = Donation::with('campaign')
            ->whereNotNull('campaign_id')
            ->get()
            ->groupBy(function ($donation) {
                return $donation->campaign ? $donation->campaign->name : 'No Campaign';
            });

        $labels = [];
        $amounts = [];
        $counts = [];

        foreach ($donations as $campaignName => $campaignDonations) {
            $labels[] = (string) $campaignName;
            $amounts[] = round((float) $campaignDonations->sum('amount'), 2);
            $counts[] = (int) $campaignDonations->count();
        }

        $this->chartData = [
            'labels' => $labels,
            'amounts' => $amounts,
            'counts' => $counts,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.donations-by-campaign-chart');
    }
}
