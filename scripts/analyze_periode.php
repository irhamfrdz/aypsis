<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== ANALISIS PERIODE KONTAINER ===\n\n";

// Contoh data kontainer tanpa tanggal akhir
echo "1. KONTAINER TANPA TANGGAL AKHIR (Periode harus berjalan otomatis):\n";
$ongoingContainers = DaftarTagihanKontainerSewa::whereNull('tanggal_akhir')
    ->orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->take(5)
    ->get();

foreach ($ongoingContainers as $container) {
    $startDate = Carbon::parse($container->tanggal_awal);
    $currentDate = Carbon::now();
    $monthsDiff = $startDate->diffInMonths($currentDate);
    $calculatedPeriode = $monthsDiff + $container->periode;

    echo "  Container: {$container->nomor_kontainer}\n";
    echo "  - Periode DB: {$container->periode}\n";
    echo "  - Tanggal mulai: {$container->tanggal_awal}\n";
    echo "  - Selisih bulan dari mulai: {$monthsDiff}\n";
    echo "  - Periode terhitung: {$calculatedPeriode}\n";
    echo "  - Group: {$container->group}\n\n";
}

// Contoh data kontainer dengan tanggal akhir
echo "2. KONTAINER DENGAN TANGGAL AKHIR (Periode tetap):\n";
$completedContainers = DaftarTagihanKontainerSewa::whereNotNull('tanggal_akhir')
    ->orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->take(5)
    ->get();

foreach ($completedContainers as $container) {
    echo "  Container: {$container->nomor_kontainer}\n";
    echo "  - Periode: {$container->periode}\n";
    echo "  - Tanggal mulai: {$container->tanggal_awal}\n";
    echo "  - Tanggal akhir: {$container->tanggal_akhir}\n";
    echo "  - Group: {$container->group}\n\n";
}

// Analisis untuk kontainer yang sama dengan periode berbeda
echo "3. CONTOH SATU KONTAINER DENGAN MULTIPLE PERIODE:\n";
$sampleContainer = DaftarTagihanKontainerSewa::select('nomor_kontainer')
    ->groupBy('nomor_kontainer')
    ->havingRaw('COUNT(*) > 3')
    ->first();

if ($sampleContainer) {
    $allPeriods = DaftarTagihanKontainerSewa::where('nomor_kontainer', $sampleContainer->nomor_kontainer)
        ->orderBy('periode')
        ->get();

    echo "  Container: {$sampleContainer->nomor_kontainer}\n";
    foreach ($allPeriods as $period) {
        echo "  - Periode {$period->periode}: {$period->tanggal_awal} s/d " . ($period->tanggal_akhir ?? 'Ongoing') . "\n";
    }
}

echo "\n=== KESIMPULAN ===\n";
echo "Tanggal sekarang: " . Carbon::now()->format('Y-m-d') . "\n";
echo "Logika periode otomatis: periode_db + selisih_bulan_dari_mulai\n";
