<?php

namespace App\Livewire\Dashboard;

use App\Models\Pledge;
use Livewire\Component;

class PendingPledgesWidget extends Component
{
  public $count;
  public $totalAmount;

  public function mount()
  {
    $this->loadData();
  }

  public function loadData()
  {
    $pendingPledges = Pledge::pending()->get();

    $this->count = $pendingPledges->count();
    $this->totalAmount = $pendingPledges->sum('amount');
  }

  public function render()
  {
    return view('livewire.dashboard.pending-pledges-widget');
  }
}
