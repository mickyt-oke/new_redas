<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NisData extends Model
{
    protected $fillable = [
        'directorate_id',
        'cadre',
        'male',
        'female',
        'total',
        'zone',
        'period',
    ];
}
