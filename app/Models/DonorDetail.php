<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonorDetail extends Model
{
    /** @use HasFactory<\Database\Factories\DonorDetailFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'donor_details';

    protected $fillable = [
        'donor_id',
        'first_name',
        'last_name',
        'birthday',
        'occupation',
        'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
