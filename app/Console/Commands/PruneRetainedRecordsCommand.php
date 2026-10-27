<?php

namespace App\Console\Commands;

use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneRetainedRecordsCommand extends Command
{
    protected $signature = 'redas:prune-retained-records {--days=2555}';

    protected $description = 'Permanently remove soft-deleted returns whose retention period has expired.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = 0;
        $disk = Storage::disk();

        Application::onlyTrashed()
            ->where(function ($query) use ($cutoff) {
                $query->where('retention_expires_at', '<', $cutoff)
                    ->orWhere(function ($q) use ($cutoff) {
                        $q->whereNull('retention_expires_at')
                            ->where('deleted_at', '<', $cutoff);
                    });
            })
            ->chunkById(100, function ($applications) use (&$count, $disk) {
                foreach ($applications as $application) {
                    $data = is_array($application->return_data) ? $application->return_data : [];

                    foreach (['supporting_documents', 'attachments'] as $key) {
                        foreach ((array) ($data[$key] ?? []) as $path) {
                            if (is_string($path) && $path !== '' && $disk->exists($path)) {
                                $disk->delete($path);
                            }
                        }
                    }

                    $application->forceDelete();
                    $count++;
                }
            });

        $this->info("Permanently removed {$count} expired return(s).");

        return self::SUCCESS;
    }
}
