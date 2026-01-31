<?php

namespace App\Jobs;

use App\Models\GivebutterContactStaging;
use App\Models\ProcessorSyncRun;
use App\Processor;
use App\Services\GivebutterContactToDonorMapper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LoadStagedGivebutterToDonors implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 3600;

    /**
     * If set, only load staging rows from this sync run. Otherwise load all not-yet-loaded.
     */
    public function __construct(
        public ?int $syncRunId = null
    ) {}

    public function handle(GivebutterContactToDonorMapper $mapper): void
    {
        $query = GivebutterContactStaging::query()
            ->whereNull('loaded_at');

        if ($this->syncRunId !== null) {
            $query->where('sync_run_id', $this->syncRunId);
        }

        $loaded = 0;
        $skipped = 0;

        $query->orderBy('id')->chunk(100, function ($rows) use ($mapper, &$loaded, &$skipped) {
            foreach ($rows as $row) {
                $payload = $row->payload;
                if (! is_array($payload)) {
                    $skipped++;
                    continue;
                }
                try {
                    $donor = $mapper->upsertDonorFromContact($payload);
                    if ($donor !== null) {
                        $row->update(['loaded_at' => now()]);
                        $loaded++;
                    } else {
                        $skipped++;
                    }
                } catch (\Throwable $e) {
                    Log::warning('LoadStagedGivebutterToDonors: skip row', [
                        'staging_id' => $row->id,
                        'givebutter_contact_id' => $row->givebutter_contact_id,
                        'message' => $e->getMessage(),
                    ]);
                    $skipped++;
                }
            }
        });

        if ($this->syncRunId !== null) {
            $count = GivebutterContactStaging::where('sync_run_id', $this->syncRunId)
                ->whereNotNull('loaded_at')
                ->count();
            ProcessorSyncRun::where('id', $this->syncRunId)->update(['contacts_loaded' => $count]);
        }
    }
}
