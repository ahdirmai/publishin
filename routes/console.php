<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('publishin:process-scheduled')->everyMinute();
Schedule::command('publishin:fetch-account-analytics')->dailyAt('02:00');
