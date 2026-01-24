<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    /** @use HasFactory<\Database\Factories\ChartOfAccountFactory> */
    use HasFactory, SoftDeletes;
    
    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'processor_id',
        'processor',
        'code',
        'name',
        'description',
        'type',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'account_id');
    }
}
