<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalanBongkaran;
use App\Models\Bl;

echo "=== Pengecekan Data Surat Jalan Bongkaran ===\n\n";

// Total surat jalan bongkaran
$totalSuratJalan = SuratJalanBongkaran::count();
echo "Total Surat Jalan Bongkaran: {$totalSuratJalan}\n\n";

// Ambil semua data kapal dan voyage yang ada di surat jalan
$suratJalans = SuratJalanBongkaran::select('nama_kapal', 'no_voyage', 'nomor_surat_jalan', 'created_at')
    ->orderBy('created_at', 'desc')
    ->get();

echo "Detail Surat Jalan yang sudah dibuat:\n";
echo str_repeat("=", 100) . "\n";
printf("%-5s %-20s %-15s %-30s %-20s\n", "No", "Nama Kapal", "No Voyage", "Nomor Surat Jalan", "Tanggal Dibuat");
echo str_repeat("=", 100) . "\n";

foreach ($suratJalans as $index => $sj) {
    printf("%-5s %-20s %-15s %-30s %-20s\n", 
        $index + 1,
        $sj->nama_kapal ?? 'N/A',
        $sj->no_voyage ?? 'N/A',
        $sj->nomor_surat_jalan ?? 'N/A',
        $sj->created_at ? $sj->created_at->format('Y-m-d H:i:s') : 'N/A'
    );
}

echo "\n\n=== Total BL per Kapal dan Voyage ===\n";
$blByKapalVoyage = Bl::select('nama_kapal', 'no_voyage')
    ->selectRaw('COUNT(*) as total')
    ->groupBy('nama_kapal', 'no_voyage')
    ->get();

foreach ($blByKapalVoyage as $bl) {
    echo "\nKapal: {$bl->nama_kapal}, Voyage: {$bl->no_voyage}\n";
    echo "  - Total BL: {$bl->total}\n";
    
    // Hitung berapa yang sudah punya surat jalan
    $blWithSj = Bl::where('nama_kapal', $bl->nama_kapal)
        ->where('no_voyage', $bl->no_voyage)
        ->whereHas('suratJalanBongkaran')
        ->count();
    
    echo "  - Sudah ada Surat Jalan: {$blWithSj}\n";
    echo "  - Belum ada Surat Jalan: " . ($bl->total - $blWithSj) . "\n";
}

echo "\n";
