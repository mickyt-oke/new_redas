<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimaryLocationCode extends Model
{
    /** @use HasFactory<\Database\Factories\PrimaryLocationCodeFactory> */
    use HasFactory;

    protected $table = 'primary_location_codes';

    protected $fillable = [
        'code',
        'location_name',
    ];

    public $timestamps = true;
}
