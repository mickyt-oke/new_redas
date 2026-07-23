<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimaryLocationType extends Model
{
    /** @use HasFactory<\Database\Factories\PrimaryLocationTypeFactory> */
    use HasFactory;

    protected $table = 'primary_location_types';

    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
}
