<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = App\Models\Absensi::whereHas('karyawan', function($q) {
    $q->where('nama_lengkap', 'like', '%AHIM%');
})->orderBy('waktu')->get(['tipe', 'waktu']);

echo json_encode($logs, JSON_PRETTY_PRINT);
