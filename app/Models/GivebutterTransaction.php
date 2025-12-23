<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GivebutterTransaction extends Model
{
  use HasFactory;

  protected $fillable = [
    'givebutter_id',
    'payload',
    'status',
  ];

  protected $casts = [
    'payload' => 'array',
  ];
}
