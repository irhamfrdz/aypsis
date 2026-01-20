<?php
/**
 * Script untuk memperbaiki data tagihan kontainer yang macet di periode 1
 * Membuat periode berikutnya berdasarkan tanggal_awal sampai sekarang
 * 
 * Jalankan dengan: php fix_create_next_periode.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Script Perbaikan Data Periode Tagihan Kontainer\n";
echo "Tanggal: " . now()->format('d M Y H:i:s') . "\n";
echo "===========================================\n\n";

// Get semua kontainer yang hanya punya periode 1 tapi seharusnya punya lebih
$stuckContainers = DaftarTagihanKontainerSewa::select('vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir', 'tarif')
    ->whereNotNull('tanggal_awal')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir', 'tarif')
    ->havingRaw('MAX(periode) = 1')
    ->get();

echo "📦 Ditemukan " . $stuckContainers->count() . " kontainer yang hanya memiliki periode 1\n\n";

if ($stuckContainers->isEmpty()) {
    echo "✅ Tidak ada kontainer yang perlu diperbaiki!\n";
    exit(0);
}

// Analyze which containers should have more periods
$needsUpdate = collect();
$currentDate = Carbon::now();

foreach ($stuckContainers as $container) {
    try {
        $startDate = Carbon::parse($container->tanggal_awal);
        $monthsElapsed = intval($startDate->diffInMonths($currentDate));
        
        // Jika sudah lebih dari 1 bulan sejak tanggal awal, harusnya ada periode 2+
        if ($monthsElapsed >= 1) {
            $expectedPeriods = $monthsElapsed + 1;
            $needsUpdate->push([
                'vendor' => $container->vendor,
                'nomor_kontainer' => $container->nomor_kontainer,
                'size' => $container->size,
                'tanggal_awal' => $container->tanggal_awal,
                'tanggal_akhir' => $container->tanggal_akhir,
                'tarif' => $container->tarif,
                'start_date' => $startDate,
                'months_elapsed' => $monthsElapsed,
                'expected_periods' => $expectedPeriods
            ]);
        }
    } catch (\Exception $e) {
        echo "⚠ Error parsing tanggal untuk {$container->nomor_kontainer}: {$e->getMessage()}\n";
    }
}

echo "📊 Kontainer yang perlu periode baru: " . $needsUpdate->count() . "\n\n";

if ($needsUpdate->isEmpty()) {
    echo "✅ Semua kontainer sudah up-to-date!\n";
    exit(0);
}

// Show sample of what will be fixed
echo "📋 Sample 10 kontainer yang akan diperbaiki:\n";
echo str_repeat("-", 100) . "\n";
printf("%-20s %-15s %-15s %-20s %-10s %-10s\n", 
    "Nomor Kontainer", "Vendor", "Size", "Tanggal Awal", "Bulan", "Expected");
echo str_repeat("-", 100) . "\n";

foreach ($needsUpdate->take(10) as $item) {
    printf("%-20s %-15s %-15s %-20s %-10d %-10d\n",
        $item['nomor_kontainer'],
        $item['vendor'] ?? '-',
        $item['size'] ?? '-',
        Carbon::parse($item['tanggal_awal'])->format('d M Y'),
        $item['months_elapsed'],
        $item['expected_periods']
    );
}

if ($needsUpdate->count() > 10) {
    echo "... dan " . ($needsUpdate->count() - 10) . " kontainer lainnya\n";
}

echo str_repeat("-", 100) . "\n\n";

// Confirm before proceeding
echo "⚠ Apakah Anda yakin ingin membuat periode baru untuk " . $needsUpdate->count() . " kontainer? (y/n): ";
$confirmation = trim(fgets(STDIN));

if (strtolower($confirmation) !== 'y') {
    echo "❌ Operasi dibatalkan.\n";
    exit(0);
}

echo "\n===========================================\n";
echo "Memulai proses pembuatan periode...\n";
echo "===========================================\n\n";

$totalCreated = 0;
$successCount = 0;
$errorCount = 0;
$errors = [];

DB::beginTransaction();

try {
    foreach ($needsUpdate as $item) {
        try {
            $startDate = $item['start_date'];
            
            // Get base record (periode 1)
            $baseRecord = DaftarTagihanKontainerSewa::where('vendor', $item['vendor'])
                ->where('nomor_kontainer', $item['nomor_kontainer'])
                ->where('tanggal_awal', $item['tanggal_awal'])
                ->where('periode', 1)
                ->first();
            
            if (!$baseRecord) {
                $errors[] = "Base record tidak ditemukan untuk {$item['nomor_kontainer']}";
                $errorCount++;
                continue;
            }
            
            // Get pricelist untuk hitung DPP
            $pricelist = MasterPricelistSewaKontainer::where('vendor', $item['vendor'])
                ->where('ukuran_kontainer', $item['size'])
                ->where('tarif', 'Bulanan')
                ->first();
            
            $baseDpp = $pricelist ? (float) $pricelist->harga : (float) $baseRecord->dpp;
            
            // Create missing periods
            $periodeCreated = 0;
            for ($periode = 2; $periode <= $item['expected_periods']; $periode++) {
                // Check if already exists
                $exists = DaftarTagihanKontainerSewa::where('vendor', $item['vendor'])
                    ->where('nomor_kontainer', $item['nomor_kontainer'])
                    ->where('tanggal_awal', $item['tanggal_awal'])
                    ->where('periode', $periode)
                    ->exists();
                
                if ($exists) {
                    continue;
                }
                
                // Calculate period dates
                $periodStart = $startDate->copy()->addMonthsNoOverflow($periode - 1);
                $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
                
                // Check if period end should be capped by tanggal_akhir
                if ($item['tanggal_akhir']) {
                    $containerEnd = Carbon::parse($item['tanggal_akhir']);
                    if ($periodStart->gt($containerEnd)) {
                        // Period starts after contract end - skip
                        continue;
                    }
                    if ($periodEnd->gt($containerEnd)) {
                        $periodEnd = $containerEnd;
                    }
                }
                
                // Calculate days in period
                $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
                $daysInFullMonth = $periodStart->daysInMonth;
                $isFullMonth = $daysInPeriod >= $daysInFullMonth;
                
                // Calculate DPP based on days
                $periodDpp = $isFullMonth ? $baseDpp : round($baseDpp * ($daysInPeriod / $daysInFullMonth), 2);
                
                // Generate masa string
                $months = [
                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                    5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                    9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
                ];
                $masaStr = $periodStart->format('j') . ' ' . $months[(int)$periodStart->format('n')] . ' ' . $periodStart->format('Y') 
                         . ' - ' . $periodEnd->format('j') . ' ' . $months[(int)$periodEnd->format('n')] . ' ' . $periodEnd->format('Y');
                
                // Create new record
                $newRecord = DaftarTagihanKontainerSewa::create([
                    'vendor' => $item['vendor'],
                    'nomor_kontainer' => $item['nomor_kontainer'],
                    'size' => $item['size'],
                    'tanggal_awal' => $item['tanggal_awal'],
                    'tanggal_akhir' => $periodEnd->format('Y-m-d'),
                    'periode' => $periode,
                    'masa' => $masaStr,
                    'tarif' => $isFullMonth ? 'Bulanan' : 'Harian',
                    'dpp' => $periodDpp,
                    'group' => $baseRecord->group,
                    'status' => $baseRecord->status,
                ]);
                
                // Model will auto-calculate dpp_nilai_lain, ppn, pph, grand_total via boot()
                
                $periodeCreated++;
                $totalCreated++;
            }
            
            if ($periodeCreated > 0) {
                echo "✅ {$item['nomor_kontainer']}: dibuat {$periodeCreated} periode baru (periode 2-{$item['expected_periods']})\n";
                $successCount++;
            }
            
        } catch (\Exception $e) {
            $errors[] = "{$item['nomor_kontainer']}: {$e->getMessage()}";
            $errorCount++;
        }
    }
    
    DB::commit();
    
    echo "\n===========================================\n";
    echo "✅ PROSES SELESAI!\n";
    echo "===========================================\n";
    echo "   - Kontainer diproses: {$successCount}\n";
    echo "   - Total periode baru dibuat: {$totalCreated}\n";
    echo "   - Error: {$errorCount}\n\n";
    
    if (!empty($errors)) {
        echo "⚠ Daftar Error:\n";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "   - {$error}\n";
        }
        if (count($errors) > 10) {
            echo "   ... dan " . (count($errors) - 10) . " error lainnya\n";
        }
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR FATAL: " . $e->getMessage() . "\n";
    echo "   Semua perubahan dibatalkan.\n";
    exit(1);
}

echo "\n===========================================\n";
echo "Script selesai dijalankan!\n";
echo "===========================================\n";
