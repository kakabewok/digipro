<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled tasks
Schedule::command('orders:cancel-expired')->everyMinute();
Schedule::command('backup:run-daily')->daily()->at('02:00');
Schedule::command('stock:check-low')->everyThirtyMinutes();
