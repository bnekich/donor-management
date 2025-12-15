<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Individual extends Model
{
    /** @use HasFactory<\Database\Factories\IndividualFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'county',
        'state',
        'zip',
        'country',
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
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
