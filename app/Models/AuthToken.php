<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class AuthToken extends Model
{
    use Prunable;

    protected $table = 'auth_tokens';

    protected $fillable = [
        'user_id',
        'type',
        'token_hash',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Prune auth tokens that expired more than a week ago.
     */
    public function prunable(): Builder
    {
        return static::where('expires_at', '<', now()->subDays(7));
    }
}
