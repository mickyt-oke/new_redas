<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'tag',
        'action_url',
        'is_read',
        'payload_json',
        'error',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'payload_json' => 'array',
        'error' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
