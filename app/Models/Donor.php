<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Donor extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DonorFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'start_date',
    ];

    protected $appends = ['mediaFile'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
        ];
    }
    public function getMediaFileAttribute()
    {
        if ($this->relationLoaded('media')) {
            return $this->getFirstMedia();
        }
        return null;
    }
}
