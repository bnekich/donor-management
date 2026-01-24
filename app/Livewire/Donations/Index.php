<?php

namespace App\Livewire\Donations;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'campaign', except: '')]
    public ?int $selectedCampaign = null;

    #[Url(as: 'donor', except: '')]
    public ?int $selectedDonor = null;

    #[Url(as: 'sort', except: 'transaction_date')]
    public string $sortField = 'transaction_date';

    #[Url(as: 'direction', except: 'desc')]
    public string $sortDirection = 'desc';

    public ?int $selectedDonationId = null;

    public bool $showDetailModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCampaign(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedDonor(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function selectDonation(int $donationId): void
    {
        $this->selectedDonationId = $donationId;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedDonationId = null;
    }

    public function render()
    {
        return view('livewire.donations.index', [
            'donations' => Donation::query()
                ->with(['donor.donorDetail', 'campaign', 'account'])
                ->when($this->search, function (Builder $query) {
                    $query->where(function (Builder $q) {
                        $q->where('reference_number', 'like', "%{$this->search}%")
                            ->orWhere('transaction_id', 'like', "%{$this->search}%")
                            ->orWhereHas('donor', function (Builder $q) {
                                $q->where('email', 'like', "%{$this->search}%")
                                    ->orWhereHas('donorDetail', function (Builder $q) {
                                        $q->where('first_name', 'like', "%{$this->search}%")
                                            ->orWhere('last_name', 'like', "%{$this->search}%");
                                    });
                            });
                    });
                })
                ->when($this->selectedCampaign, function (Builder $query) {
                    $query->where('campaign_id', $this->selectedCampaign);
                })
                ->when($this->selectedDonor, function (Builder $query) {
                    $query->where('donor_id', $this->selectedDonor);
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(15),
            'campaigns' => Campaign::orderBy('name')->get(),
            'donors' => Donor::has('donations')->with('donorDetail')->orderBy('email')->get(),
            'selectedDonation' => $this->selectedDonationId
                ? Donation::with(['donor.donorDetail', 'campaign', 'account', 'pledge'])->find($this->selectedDonationId)
                : null,
        ]);
    }
}
