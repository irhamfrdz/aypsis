<?php
/**
 * Script diagnostik untuk melihat data kontainer periode 1
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "===========================================\n";
echo "Diagnostik Kontainer Periode 1\n";
echo "===========================================\n\n";

// Cek sample kontainer
$sample = DaftarTagihanKontainerSewa::where('periode', 1)
    ->whereNotNull('tanggal_awal')
    ->first();

if ($sample) {
    echo "Sample Kontainer:\n";
    echo "  - Nomor: {$sample->nomor_kontainer}\n";
    echo "  - Vendor: {$sample->vendor}\n";
    echo "  - Size: {$sample->size}\n";
    echo "  - Tanggal Awal: {$sample->tanggal_awal}\n";
    echo "  - Tanggal Akhir: {$sample->tanggal_akhir}\n";
    echo "  - DPP: " . number_format($sample->dpp ?? 0, 0, ',', '.') . "\n";
    echo "\n";
    
    $startDate = Carbon::parse($sample->tanggal_awal);
    $now = Carbon::now();
    $monthsElapsed = $startDate->diffInMonths($now);
    
    echo "Analisis:\n";
    echo "  - Bulan sejak mulai: {$monthsElapsed}\n";
    echo "  - Expected periods: " . ($monthsElapsed + 1) . "\n";
    
    // Cek apakah sudah ada periode lain
    $existingPeriods = DaftarTagihanKontainerSewa::where('nomor_kontainer', $sample->nomor_kontainer)
        ->where('tanggal_awal', $sample->tanggal_awal)
        ->where('vendor', $sample->vendor)
        ->pluck('periode')
        ->toArray();
    
    echo "  - Periode yang ada: " . implode(', ', $existingPeriods) . "\n\n";
}

// Cek apakah ada kontainer yang sudah punya multiple periode
$multiPeriod = DaftarTagihanKontainerSewa::select('nomor_kontainer')
    ->groupBy('nomor_kontainer')
    ->havingRaw('MAX(periode) > 1')
    ->limit(5)
    ->get();

echo "Kontainer dengan multiple periode (sample 5):\n";
foreach ($multiPeriod as $c) {
    $maxPeriode = DaftarTagihanKontainerSewa::where('nomor_kontainer', $c->nomor_kontainer)->max('periode');
    echo "  - {$c->nomor_kontainer}: max periode = {$maxPeriode}\n";
}

echo "\n";

// Cek total unique containers
$totalUnique = DaftarTagihanKontainerSewa::distinct('nomor_kontainer')->count();
$periode1Only = DaftarTagihanKontainerSewa::select('nomor_kontainer')
    ->whereNotNull('tanggal_awal')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('nomor_kontainer')
    ->havingRaw('MAX(periode) = 1')
    ->count();

echo "Statistik:\n";
echo "  - Total kontainer unik: {$totalUnique}\n";
echo "  - Kontainer dengan periode 1 saja: {$periode1Only}\n";

// Cek kontainer yang benar-benar butuh update
$needsUpdate = 0;
$stuckContainers = DaftarTagihanKontainerSewa::select('nomor_kontainer', 'vendor', 'tanggal_awal', 'tanggal_akhir')
    ->whereNotNull('tanggal_awal')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('nomor_kontainer', 'vendor', 'tanggal_awal', 'tanggal_akhir')
    ->havingRaw('MAX(periode) = 1')
    ->get();

foreach ($stuckContainers as $container) {
    try {
        $startDate = Carbon::parse($container->tanggal_awal);
        $monthsElapsed = intval($startDate->diffInMonths(Carbon::now()));
        
        // Jika sudah lebih dari 1 bulan tapi tanggal_akhir belum lewat
        if ($monthsElapsed >= 1) {
            // Cek apakah kontainer masih aktif (tanggal_akhir belum lewat atau null)
            $isActive = true;
            if ($container->tanggal_akhir) {
                $endDate = Carbon::parse($container->tanggal_akhir);
                $isActive = $endDate->gte($startDate->copy()->addMonthsNoOverflow(1));
            }
            
            if ($isActive) {
                $needsUpdate++;
            }
        }
    } catch (\Exception $e) {
        // skip
    }
}

echo "  - Kontainer yang perlu periode baru: {$needsUpdate}\n";

echo "\n===========================================\n";
