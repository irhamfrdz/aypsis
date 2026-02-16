<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('tanda-terima:update-penerima', function () {
    $this->call('tanda-terima:update-penerima');
})->purpose('Update data penerima pada tanda terima')->everyThirtyMinutes();

Artisan::command('manifest:update-penerima', function () {
    $this->call('manifest:update-penerima');
})->purpose('Update data penerima pada manifest')->everyThirtyMinutes();
