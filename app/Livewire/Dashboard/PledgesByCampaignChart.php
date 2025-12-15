<?php

namespace App\Livewire\Dashboard;

use App\Models\Pledge;
use Livewire\Component;

class PledgesByCampaignChart extends Component
{
  public $chartData;

  public function mount()
  {
    $this->loadData();
    $this->dispatch('render-chart');
  }

  public function loadData()
  {
    $pledges = Pledge::with('campaign')
      ->pending()
      ->whereNotNull('campaign_id')
      ->get()
      ->groupBy('campaign.name');

    $labels = [];
    $data = [];

    foreach ($pledges as $campaignName => $campaignPledges) {
      $labels[] = $campaignName;
      $data[] = $campaignPledges->sum('amount');
    }

    $this->chartData = [
      'labels' => $labels,
      'datasets' => [
        [
          'data' => $data,
          'backgroundColor' => [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
          ],
        ],
      ],
    ];
  }

  public function render()
  {
    return view('livewire.dashboard.pledges-by-campaign-chart');
  }
}
