<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'goal_amount',
        'raised_amount',
        'is_active',
    ];

    public function donors()
    {
        return $this->belongsToMany(Donor::class, 'donors_campaigns');
    }
}
