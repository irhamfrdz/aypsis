<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('attendance:sync-local')->everyTenMinutes();
Schedule::command('tanda-terima:update-penerima')->everyTenMinutes();
Schedule::command('manifest:update-penerima')->everyTenMinutes();
