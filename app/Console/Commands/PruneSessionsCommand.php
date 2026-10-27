<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneSessionsCommand extends Command
{
    protected $signature = 'session:prune {--days=30}';

    protected $description = 'Remove sessions older than the specified number of days.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $cutoff->getTimestamp())
            ->delete();

        $this->info("Pruned {$deleted} session(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
