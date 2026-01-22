<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Donor extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DonorFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'processor_id',
        'processor',
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
    ];

    protected $appends = ['mediaFile', 'name'];

    protected function casts(): array
    {
        return [];
    }

    public function getMediaFileAttribute()
    {
        if ($this->relationLoaded('media')) {
            return $this->getFirstMedia();
        }

        return null;
    }

     public function getNameAttribute(): string
     {

    //     if ($this->relationLoaded('organization') && $this->organization) {
    //         return $this->organization->name;
    //     }

         $this->loadMissing(['donorDetail']);

    //     if ($this->organization) {
    //         return $this->organization->name;
    //     }

         return (string) $this->email;
     }

    public function donorDetail(): HasOne
    {
        return $this->hasOne(DonorDetail::class);
    }

//    public function organization(): HasOne
//    {
//        return $this->hasOne(Organization::class);
//    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

}
