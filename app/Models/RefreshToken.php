<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends Model
{
    use Prunable;

    protected $table = 'refresh_tokens';

    protected $fillable = [
        'user_id',
        'jti',
        'token_hash',
        'expires_at',
        'revoked_at',
        'created_ip',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Prune refresh tokens that have expired or been revoked.
     */
    public function prunable(): Builder
    {
        return static::where('expires_at', '<', now()->subDays(30))
            ->orWhereNotNull('revoked_at');
    }
}
