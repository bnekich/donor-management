<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessorSyncRun extends Model
{
    protected $table = 'processor_sync_runs';

    protected $fillable = [
        'processor',
        'started_at',
        'finished_at',
        'status',
        'pages_fetched',
        'contacts_staged',
        'contacts_loaded',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function givebutterStagingContacts(): HasMany
    {
        return $this->hasMany(GivebutterContactStaging::class, 'sync_run_id');
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
