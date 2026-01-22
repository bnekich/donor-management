<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'processor_id',
        'processor',
        'code',
        'name',
        'description',
        'start_date',
        'end_date',
        'goal',
    ];

    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }
}
