<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'goal',
    ];

    // public function donors()
    // {
    //     return $this->belongsToMany(Donor::class, 'donors_campaigns');
    // }

    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }
}
