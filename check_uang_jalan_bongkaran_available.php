<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalanBongkaran;
use App\Models\UangJalanBongkaran;

echo "=== Pengecekan Surat Jalan Bongkaran untuk Uang Jalan ===\n\n";

// Ambil semua surat jalan bongkaran
$suratJalans = SuratJalanBongkaran::orderBy('created_at', 'desc')->get();

echo "Total Surat Jalan Bongkaran: " . $suratJalans->count() . "\n\n";

// Cek yang sudah punya uang jalan
$withUangJalan = 0;
$withoutUangJalan = 0;

echo "Detail:\n";
echo str_repeat("=", 120) . "\n";
printf("%-5s %-25s %-20s %-20s %-15s %-30s\n", 
    "No", 
    "Nomor SJ", 
    "Supir", 
    "No Plat",
    "Status UJ",
    "Nomor Uang Jalan"
);
echo str_repeat("=", 120) . "\n";

foreach ($suratJalans as $index => $sj) {
    $uangJalan = UangJalanBongkaran::where('surat_jalan_bongkaran_id', $sj->id)->first();
    
    if ($uangJalan) {
        $withUangJalan++;
        $status = "SUDAH ADA";
        $nomorUJ = $uangJalan->nomor_uang_jalan;
    } else {
        $withoutUangJalan++;
        $status = "BELUM ADA";
        $nomorUJ = "-";
    }
    
    printf("%-5s %-25s %-20s %-20s %-15s %-30s\n",
        $index + 1,
        substr($sj->nomor_surat_jalan ?? 'N/A', 0, 25),
        substr($sj->supir ?? 'N/A', 0, 20),
        substr($sj->no_plat ?? 'N/A', 0, 20),
        $status,
        substr($nomorUJ, 0, 30)
    );
}

echo str_repeat("=", 120) . "\n";
echo "\nRingkasan:\n";
echo "- Sudah ada uang jalan: {$withUangJalan}\n";
echo "- Belum ada uang jalan: {$withoutUangJalan}\n";

// Tampilkan surat jalan bongkaran yang belum ada uang jalannya
if ($withoutUangJalan > 0) {
    echo "\n\nSurat Jalan Bongkaran yang BELUM memiliki Uang Jalan:\n";
    echo str_repeat("=", 100) . "\n";
    
    $existingUJIds = UangJalanBongkaran::pluck('surat_jalan_bongkaran_id')->toArray();
    $availableSJ = SuratJalanBongkaran::whereNotIn('id', $existingUJIds)
        ->orderBy('created_at', 'desc')
        ->get();
    
    foreach ($availableSJ as $index => $sj) {
        echo ($index + 1) . ". ID: " . $sj->id . " - " . ($sj->nomor_surat_jalan ?? 'N/A') . " - " . ($sj->supir ?? 'N/A') . " (" . ($sj->no_plat ?? 'N/A') . ")\n";
    }
}

echo "\n";
