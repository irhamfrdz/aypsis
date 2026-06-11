<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$voyage = 'SA09BJ26';
$newDate = '2026-05-08';

$updatedBls = DB::table('bls')->where('no_voyage', $voyage)->update([
    'tanggal_berangkat' => $newDate
]);

$updatedNaikKapal = DB::table('naik_kapal')->where('no_voyage', $voyage)->update([
    'tanggal_muat' => $newDate
]);

echo "Updated BLs: {$updatedBls} rows.\n";
echo "Updated Naik Kapal: {$updatedNaikKapal} rows.\n";
