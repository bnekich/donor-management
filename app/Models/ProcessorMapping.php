<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessorMapping extends Model
{
    protected $fillable = [
        'processor',
        'source_field',
        'target_field',
        'transformation_type',
        'transformation_config',
        'is_required',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'transformation_config' => 'array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }
}
