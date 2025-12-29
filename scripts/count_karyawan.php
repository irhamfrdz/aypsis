<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'TOTAL: ' . App\Models\Karyawan::count() . PHP_EOL;
echo 'AKTIF: ' . App\Models\Karyawan::whereNull('tanggal_berhenti')->count() . PHP_EOL;
echo 'BERHENTI: ' . App\Models\Karyawan::whereNotNull('tanggal_berhenti')->count() . PHP_EOL;
