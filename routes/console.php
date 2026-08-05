<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:auto-backup')->dailyAt('23:00');

$autoSyncTime = \App\Models\SchoolSetting::get('auto_sync_time', '0');
if ($autoSyncTime == '1') {
    Schedule::command('spp:sync-time')->dailyAt('00:00');
}

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
