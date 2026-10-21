<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Custom Sanctum personal access token model.
 *
 * Extends the default Sanctum model so the application can apply pruning,
 * custom scopes and audit retention policies for a government data repository.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use Prunable;

    /**
     * Prune access tokens that expired more than 90 days ago.
     */
    public function prunable(): Builder
    {
        return static::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now()->subDays(90));
    }
}
