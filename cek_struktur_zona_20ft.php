<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== CEK STRUKTUR DATA ZONA 20FT ===\n\n";

$data = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->where('size', '20')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

echo "Sample 10 data terbaru:\n";
echo str_repeat("-", 150) . "\n";
printf("%-5s %-15s %-12s %-12s %-12s %-8s %-15s %-15s\n", 
    "ID", "No Kontainer", "Tgl Awal", "Tgl Akhir", "Masa", "Periode", "DPP", "Grand Total");
echo str_repeat("-", 150) . "\n";

foreach ($data as $item) {
    // Hitung selisih hari dari tanggal_awal dan tanggal_akhir
    $selisihHari = 0;
    if ($item->tanggal_awal && $item->tanggal_akhir) {
        $selisihHari = Carbon::parse($item->tanggal_awal)->diffInDays(Carbon::parse($item->tanggal_akhir)) + 1;
    }
    
    printf("%-5s %-15s %-12s %-12s %-12s %-8s %-15s %-15s\n",
        $item->id,
        substr($item->nomor_kontainer, 0, 15),
        $item->tanggal_awal ? $item->tanggal_awal->format('Y-m-d') : '-',
        $item->tanggal_akhir ? $item->tanggal_akhir->format('Y-m-d') : '-',
        $item->masa ?? '-',
        $item->periode ?? '-',
        'Rp ' . number_format($item->dpp, 0, ',', '.'),
        'Rp ' . number_format($item->grand_total, 0, ',', '.')
    );
    
    echo "  → Selisih hari (calculated): $selisihHari hari\n";
}

echo str_repeat("-", 150) . "\n\n";

// Cek distinct values untuk masa
echo "Distinct values untuk 'masa':\n";
$masaValues = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->where('size', '20')
    ->distinct()
    ->pluck('masa')
    ->toArray();

foreach ($masaValues as $masa) {
    $count = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->where('size', '20')
        ->where('masa', $masa)
        ->count();
    echo "  - '$masa': $count kontainer\n";
}

echo "\n";

// Cek yang > 30 hari berdasarkan tanggal_awal dan tanggal_akhir
$sebulan = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
    ->where('size', '20')
    ->whereNotNull('tanggal_awal')
    ->whereNotNull('tanggal_akhir')
    ->get()
    ->filter(function($item) {
        $days = Carbon::parse($item->tanggal_awal)->diffInDays(Carbon::parse($item->tanggal_akhir)) + 1;
        return $days >= 30;
    });

echo "Kontainer ZONA 20ft yang sudah >= 30 hari (dari tanggal_awal ke tanggal_akhir): " . $sebulan->count() . "\n";

if ($sebulan->count() > 0) {
    echo "\nSample kontainer yang sudah >= 30 hari:\n";
    echo str_repeat("-", 120) . "\n";
    printf("%-5s %-15s %-10s %-15s %-15s\n", "ID", "No Kontainer", "Hari", "DPP Saat Ini", "Masa");
    echo str_repeat("-", 120) . "\n";
    
    foreach ($sebulan->take(5) as $item) {
        $days = Carbon::parse($item->tanggal_awal)->diffInDays(Carbon::parse($item->tanggal_akhir)) + 1;
        printf("%-5s %-15s %-10s %-15s %-15s\n",
            $item->id,
            substr($item->nomor_kontainer, 0, 15),
            $days . ' hari',
            'Rp ' . number_format($item->dpp, 0, ',', '.'),
            $item->masa ?? '-'
        );
    }
}
