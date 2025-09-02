<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST IMPORT NO KETENAGAKERJAAN ===\n";

// Data sample dari CSV Anda
$testData = [
    [
        'nik' => '0003',
        'nama_lengkap' => 'IWAN',
        'nama_panggilan' => 'IWAN',
        'email' => 'ibrahimbaim160685@gmail.com',
        'no_ketenagakerjaan' => '3604252706940001',
        'divisi' => 'SUPIR',
        'pekerjaan' => 'SUPIR TRAILER'
    ],
    [
        'nik' => '0006',
        'nama_lengkap' => 'DAYAT SUTORO',
        'nama_panggilan' => 'DAYAT',
        'email' => 'kangpardi1500@gmail.com',
        'no_ketenagakerjaan' => '3301090504800001',
        'divisi' => 'SUPIR',
        'pekerjaan' => 'SUPIR TRAILER'
    ]
];

foreach ($testData as $data) {
    try {
        echo "Importing NIK: {$data['nik']} - {$data['nama_lengkap']}\n";

        $karyawan = \App\Models\Karyawan::updateOrCreate(
            ['nik' => $data['nik']],
            $data
        );

        echo "✅ Berhasil: NIK {$karyawan->nik} dengan no_ketenagakerjaan: {$karyawan->no_ketenagakerjaan}\n";

    } catch (\Exception $e) {
        echo "❌ Error pada NIK {$data['nik']}: " . $e->getMessage() . "\n";
    }
    echo "---\n";
}

echo "\n=== VERIFIKASI DATA ===\n";
$imported = \App\Models\Karyawan::whereIn('nik', ['0003', '0006'])->get();
foreach ($imported as $karyawan) {
    echo "NIK: {$karyawan->nik} | Nama: {$karyawan->nama_lengkap} | No Ketenagakerjaan: {$karyawan->no_ketenagakerjaan}\n";
}
