<?php

namespace App\Console\Commands;

use App\Models\PersonalAccessToken;
use App\Models\RefreshToken;
use Illuminate\Console\Command;

class PruneExpiredTokens extends Command
{
    protected $signature = 'tokens:prune
                            {--days=90 : Retain expired access tokens for this many days}
                            {--refresh-days=90 : Retain revoked/expired refresh tokens for this many days}
                            {--dry-run : Show counts without deleting}';

    protected $description = 'Prune expired access and refresh tokens to limit audit-table growth.';

    public function handle(): int
    {
        $accessCutoff = now()->subDays((int) $this->option('days'));
        $refreshCutoff = now()->subDays((int) $this->option('refresh-days'));

        $accessQuery = PersonalAccessToken::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $accessCutoff);

        $refreshQuery = RefreshToken::query()
            ->where(function ($q) use ($refreshCutoff) {
                $q->where('expires_at', '<', $refreshCutoff)
                    ->orWhereNotNull('revoked_at');
            });

        $accessCount = $accessQuery->count();
        $refreshCount = $refreshQuery->count();

        $this->info("Expired access tokens older than {$accessCutoff->toDateString()}: {$accessCount}");
        $this->info("Revoked/expired refresh tokens older than {$refreshCutoff->toDateString()}: {$refreshCount}");

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no rows deleted.');

            return self::SUCCESS;
        }

        $deletedAccess = $accessQuery->delete();
        $deletedRefresh = $refreshQuery->delete();

        $this->info("Deleted {$deletedAccess} access tokens and {$deletedRefresh} refresh tokens.");

        return self::SUCCESS;
    }
}
