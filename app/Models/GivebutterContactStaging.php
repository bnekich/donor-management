<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GivebutterContactStaging extends Model
{
    protected $table = 'givebutter_contacts_staging';

    protected $fillable = [
        'givebutter_contact_id',
        'sync_run_id',
        'payload',
        'loaded_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'loaded_at' => 'datetime',
        ];
    }

    public function syncRun(): BelongsTo
    {
        return $this->belongsTo(ProcessorSyncRun::class, 'sync_run_id');
    }

    public function isLoaded(): bool
    {
        return $this->loaded_at !== null;
    }

    /** Get primary_email from payload (for display / mapping). */
    public function getPrimaryEmailAttribute(): ?string
    {
        return $this->payload['primary_email'] ?? null;
    }

    /** Get first_name from payload. */
    public function getFirstNameAttribute(): ?string
    {
        return $this->payload['first_name'] ?? null;
    }

    /** Get last_name from payload. */
    public function getLastNameAttribute(): ?string
    {
        return $this->payload['last_name'] ?? null;
    }
}
