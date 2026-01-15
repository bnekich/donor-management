<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    /** @use HasFactory<\Database\Factories\DonationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'processor',
        'processor_id',
        'reference_number',
        'donor_id',
        'donor_type',
        'amount',
        'processor_fee',
        'net_amount',
        'transaction_date',
        'payment_method',
        'transaction_id',
        'pledge_id',
        'campaign_id',
        'chapter_id',
        'account_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processor_fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    /**
     * Get the donor (polymorphic relationship).
     */
    public function donor(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the campaign that owns the donation.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the chapter that owns the donation.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Get the pledge associated with the donation.
     */
    public function pledge(): BelongsTo
    {
        return $this->belongsTo(Pledge::class);
    }

    /**
     * Get the chart of account associated with the donation.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
}
