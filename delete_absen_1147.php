<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nik = '1147';
$tanggal = '2026-07-16';

$deleted = \App\Models\Absensi::where('nik', $nik)
    ->whereDate('waktu', $tanggal)
    ->delete();

echo "Berhasil menghapus $deleted data absensi untuk NIK $nik pada tanggal $tanggal.\n";
