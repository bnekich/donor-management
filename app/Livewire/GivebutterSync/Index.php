<?php

namespace App\Livewire\GivebutterSync;

use App\Jobs\FetchAndStageGivebutterContacts;
use App\Jobs\LoadStagedGivebutterToDonors;
use App\Models\GivebutterContactStaging;
use App\Models\ProcessorSyncRun;
use App\Processor;
use App\Services\GivebutterContactsClient;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /** Load from a specific sync run (null = all not-yet-loaded). */
    public ?int $loadFromSyncRunId = null;

    public function startFetchAndStage(): void
    {
        FetchAndStageGivebutterContacts::dispatch();
        session()->flash('success', __('Givebutter sync job dispatched. Staging will update as pages are fetched.'));
    }

    public function startLoadFromStaging(): void
    {
        LoadStagedGivebutterToDonors::dispatch($this->loadFromSyncRunId);
        session()->flash('success', __('Load from staging job dispatched.'));
    }

    public function getApiConnectedProperty(): bool
    {
        $key = config('services.givebutter.api_key');
        if (empty($key)) {
            return false;
        }

        return GivebutterContactsClient::fromConfig()->ping();
    }

    public function render()
    {
        $runs = ProcessorSyncRun::query()
            ->where('processor', Processor::Givebutter->value)
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'runsPage');

        $stagingCount = GivebutterContactStaging::query()->count();
        $stagingNotLoadedCount = GivebutterContactStaging::query()->whereNull('loaded_at')->count();
        $stagingPreview = GivebutterContactStaging::query()
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('livewire.givebutter-sync.index', [
            'runs' => $runs,
            'stagingCount' => $stagingCount,
            'stagingNotLoadedCount' => $stagingNotLoadedCount,
            'stagingPreview' => $stagingPreview,
        ])->layout('components.layouts.app', ['title' => __('Givebutter Contacts Sync')]);
    }
}
