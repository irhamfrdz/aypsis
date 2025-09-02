<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Test import single record:\n";

$karyawan = \App\Models\Karyawan::create([
    'nik' => 'TEST001',
    'nama_lengkap' => 'Test User',
    'nama_panggilan' => 'Test',
    'no_ketenagakerjaan' => '1234567890123456'
]);

echo "Berhasil created NIK: " . $karyawan->nik . " dengan no_ketenagakerjaan: " . $karyawan->no_ketenagakerjaan . "\n";

echo "Sekarang akan hapus test record...\n";
$karyawan->delete();
echo "Test record dihapus.\n";
