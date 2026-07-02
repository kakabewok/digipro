<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper around spatie/laravel-backup for scheduled backups.
 */
class BackupDatabaseCommand extends Command
{
    protected $signature = 'backup:run-daily';

    protected $description = 'Run daily database backup using spatie/laravel-backup';

    public function handle(): int
    {
        $this->info('Starting daily backup...');

        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            $output = Artisan::output();

            Log::info('Daily backup completed', ['output' => $output]);
            $this->info('Backup completed successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Daily backup failed', ['error' => $e->getMessage()]);
            $this->error('Backup failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
