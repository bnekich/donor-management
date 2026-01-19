<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'website',
        'description',
        'type',
        'needs_review',
    ];

    protected function casts(): array
    {
        return [
            'needs_review' => 'boolean',
        ];
    }

//    public function donor(): BelongsTo
//    {
//        return $this->belongsTo(Donor::class);
//    }
}
