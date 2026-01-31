<?php

namespace App\Jobs;

use App\Models\GivebutterContactStaging;
use App\Models\ProcessorSyncRun;
use App\Processor;
use App\Services\GivebutterContactsClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchAndStageGivebutterContacts implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 3600;

    public function __construct() {}

    public function handle(GivebutterContactsClient $client): void
    {
        $run = ProcessorSyncRun::create([
            'processor' => Processor::Givebutter->value,
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $pagesFetched = 0;
            $contactsStaged = 0;

            foreach ($client->fetchAllPages() as $page) {
                $data = $page['data'] ?? [];
                $meta = $page['meta'] ?? [];

                foreach ($data as $contact) {
                    $id = $contact['id'] ?? null;
                    if ($id === null) {
                        continue;
                    }
                    GivebutterContactStaging::updateOrCreate(
                        ['givebutter_contact_id' => $id],
                        [
                            'sync_run_id' => $run->id,
                            'payload' => $contact,
                        ]
                    );
                    $contactsStaged++;
                }

                $pagesFetched++;
                $run->update([
                    'pages_fetched' => $pagesFetched,
                    'contacts_staged' => $contactsStaged,
                ]);
            }

            $run->update([
                'status' => 'success',
                'finished_at' => now(),
                'pages_fetched' => $pagesFetched,
                'contacts_staged' => $contactsStaged,
            ]);
        } catch (\Throwable $e) {
            Log::error('FetchAndStageGivebutterContacts failed', [
                'run_id' => $run->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
