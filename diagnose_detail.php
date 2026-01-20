<?php
/**
 * Script detail diagnostik
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "===========================================\n";
echo "Detail Diagnostik Periode Tagihan\n";
echo "===========================================\n\n";

// Get all unique container combinations
$containers = DaftarTagihanKontainerSewa::select('vendor', 'nomor_kontainer', 'tanggal_awal', 'tanggal_akhir')
    ->whereNotNull('tanggal_awal')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('vendor', 'nomor_kontainer', 'tanggal_awal', 'tanggal_akhir')
    ->get();

echo "Total kombinasi kontainer: " . $containers->count() . "\n\n";

// Analyze a few containers
echo "Analisis 20 kontainer pertama:\n";
echo str_repeat("-", 120) . "\n";
printf("%-18s %-10s %-12s %-12s %-10s %-8s %-8s %-8s\n", 
    "Nomor", "Vendor", "Tgl Awal", "Tgl Akhir", "Durasi", "MaxPer", "Target", "Status");
echo str_repeat("-", 120) . "\n";

$problematic = [];

foreach ($containers->take(20) as $container) {
    $startDate = Carbon::parse($container->tanggal_awal);
    
    // Calculate target periods
    if ($container->tanggal_akhir) {
        $endDate = Carbon::parse($container->tanggal_akhir);
        $monthsNeeded = max(1, intval($endDate->diffInMonths($startDate)) + 1);
    } else {
        $endDate = Carbon::now();
        $monthsNeeded = max(1, intval($endDate->diffInMonths($startDate)) + 1);
    }
    
    // Get max existing periode
    $maxExisting = DaftarTagihanKontainerSewa::where('vendor', $container->vendor)
        ->where('nomor_kontainer', $container->nomor_kontainer)
        ->where('tanggal_awal', $container->tanggal_awal)
        ->max('periode') ?? 0;
    
    $status = $maxExisting >= $monthsNeeded ? 'OK' : 'NEED FIX';
    
    if ($status === 'NEED FIX') {
        $problematic[] = [
            'container' => $container,
            'max_existing' => $maxExisting,
            'months_needed' => $monthsNeeded
        ];
    }
    
    printf("%-18s %-10s %-12s %-12s %-10d %-8d %-8d %-8s\n",
        $container->nomor_kontainer,
        $container->vendor ?? '-',
        $startDate->format('d/m/Y'),
        $container->tanggal_akhir ? Carbon::parse($container->tanggal_akhir)->format('d/m/Y') : 'ongoing',
        $monthsNeeded,
        $maxExisting,
        $monthsNeeded,
        $status
    );
}

echo str_repeat("-", 120) . "\n\n";

// Count all problematic containers
$allProblematic = 0;
foreach ($containers as $container) {
    $startDate = Carbon::parse($container->tanggal_awal);
    
    if ($container->tanggal_akhir) {
        $endDate = Carbon::parse($container->tanggal_akhir);
        $monthsNeeded = max(1, intval($endDate->diffInMonths($startDate)) + 1);
    } else {
        $monthsNeeded = max(1, intval(Carbon::now()->diffInMonths($startDate)) + 1);
    }
    
    $maxExisting = DaftarTagihanKontainerSewa::where('vendor', $container->vendor)
        ->where('nomor_kontainer', $container->nomor_kontainer)
        ->where('tanggal_awal', $container->tanggal_awal)
        ->max('periode') ?? 0;
    
    if ($maxExisting < $monthsNeeded) {
        $allProblematic++;
    }
}

echo "Total kontainer yang perlu fix: {$allProblematic}\n";

// Get overall statistics
echo "\nStatistik:\n";
$totalRecords = DaftarTagihanKontainerSewa::count();
$periode1 = DaftarTagihanKontainerSewa::where('periode', 1)->count();
$periode2Plus = DaftarTagihanKontainerSewa::where('periode', '>', 1)->count();

echo "  - Total records: {$totalRecords}\n";
echo "  - Records periode 1: {$periode1}\n";
echo "  - Records periode 2+: {$periode2Plus}\n";

echo "\n===========================================\n";
