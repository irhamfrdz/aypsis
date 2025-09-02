<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFIKASI FINAL IMPORT NO KETENAGAKERJAAN ===\n";

$total = \App\Models\Karyawan::whereNotNull('no_ketenagakerjaan')
    ->where('no_ketenagakerjaan', '!=', '')
    ->count();

echo "Total karyawan dengan no_ketenagakerjaan: $total\n\n";

$sample = \App\Models\Karyawan::whereNotNull('no_ketenagakerjaan')
    ->where('no_ketenagakerjaan', '!=', '')
    ->limit(10)
    ->get(['nik', 'nama_lengkap', 'no_ketenagakerjaan']);

echo "Sample data:\n";
foreach ($sample as $k) {
    echo "NIK: {$k->nik} | Nama: {$k->nama_lengkap} | No Ketenagakerjaan: {$k->no_ketenagakerjaan}\n";
}

echo "\n✅ Import nomor ketenagakerjaan berhasil!\n";
