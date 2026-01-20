<?php
/**
 * Script untuk membuat periode berikutnya untuk SEMUA kontainer
 * termasuk yang sudah expired (tanggal_akhir sudah lewat)
 * 
 * Jalankan dengan: php force_create_all_periode.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Script Membuat SEMUA Periode Tagihan Kontainer\n";
echo "(Termasuk kontainer yang sudah expired)\n";
echo "Tanggal: " . now()->format('d M Y H:i:s') . "\n";
echo "===========================================\n\n";

// Get semua kontainer dengan periode 1 yang seharusnya punya lebih
$containers = DaftarTagihanKontainerSewa::select('vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir', 'tarif', 'group', 'status')
    ->where('periode', 1)
    ->whereNotNull('tanggal_awal')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->get();

echo "📦 Total kontainer periode 1: " . $containers->count() . "\n\n";

if ($containers->isEmpty()) {
    echo "✅ Tidak ada kontainer yang perlu diperbaiki!\n";
    exit(0);
}

$needsUpdate = collect();
$currentDate = Carbon::now();

foreach ($containers as $container) {
    try {
        $startDate = Carbon::parse($container->tanggal_awal);
        
        // Tentukan sampai kapan harus buat periode
        // Jika ada tanggal_akhir, gunakan itu. Jika tidak, gunakan sekarang.
        if ($container->tanggal_akhir) {
            $endDate = Carbon::parse($container->tanggal_akhir);
        } else {
            $endDate = $currentDate;
        }
        
        // Hitung berapa bulan dari start sampai end
        $monthsNeeded = max(1, intval($startDate->diffInMonths($endDate)) + 1);
        
        // Cek periode tertinggi yang sudah ada
        $maxExistingPeriode = DaftarTagihanKontainerSewa::where('vendor', $container->vendor)
            ->where('nomor_kontainer', $container->nomor_kontainer)
            ->where('tanggal_awal', $container->tanggal_awal)
            ->max('periode') ?? 0;
        
        // Jika masih butuh periode baru
        if ($monthsNeeded > $maxExistingPeriode) {
            $needsUpdate->push([
                'vendor' => $container->vendor,
                'nomor_kontainer' => $container->nomor_kontainer,
                'size' => $container->size,
                'tanggal_awal' => $container->tanggal_awal,
                'tanggal_akhir' => $container->tanggal_akhir,
                'tarif' => $container->tarif,
                'group' => $container->group,
                'status' => $container->status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'max_existing' => $maxExistingPeriode,
                'months_needed' => $monthsNeeded
            ]);
        }
    } catch (\Exception $e) {
        echo "⚠ Error parsing untuk {$container->nomor_kontainer}: {$e->getMessage()}\n";
    }
}

echo "📊 Kontainer yang perlu periode baru: " . $needsUpdate->count() . "\n\n";

if ($needsUpdate->isEmpty()) {
    echo "✅ Semua kontainer sudah up-to-date!\n";
    exit(0);
}

// Show sample
echo "📋 Sample 10 kontainer yang akan diperbaiki:\n";
echo str_repeat("-", 110) . "\n";
printf("%-18s %-12s %-6s %-15s %-15s %-8s %-8s\n", 
    "Nomor", "Vendor", "Size", "Tgl Awal", "Tgl Akhir", "Existing", "Target");
echo str_repeat("-", 110) . "\n";

foreach ($needsUpdate->take(10) as $item) {
    printf("%-18s %-12s %-6s %-15s %-15s %-8d %-8d\n",
        $item['nomor_kontainer'],
        $item['vendor'] ?? '-',
        $item['size'] ?? '-',
        Carbon::parse($item['tanggal_awal'])->format('d M Y'),
        $item['tanggal_akhir'] ? Carbon::parse($item['tanggal_akhir'])->format('d M Y') : 'ongoing',
        $item['max_existing'],
        $item['months_needed']
    );
}

if ($needsUpdate->count() > 10) {
    echo "... dan " . ($needsUpdate->count() - 10) . " kontainer lainnya\n";
}

echo str_repeat("-", 110) . "\n\n";

// Calculate total periods to create
$totalPeriodsToCreate = $needsUpdate->sum(function($item) {
    return $item['months_needed'] - $item['max_existing'];
});

echo "📊 Total periode yang akan dibuat: ~{$totalPeriodsToCreate}\n\n";

echo "⚠ Lanjutkan membuat periode? (y/n): ";
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
    foreach ($needsUpdate as $index => $item) {
        try {
            $startDate = $item['start_date'];
            $endDate = $item['end_date'];
            
            // Get base record (periode 1) for defaults
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
            $pricelist = null;
            if ($item['vendor'] && $item['size']) {
                $pricelist = MasterPricelistSewaKontainer::where('vendor', $item['vendor'])
                    ->where('ukuran_kontainer', $item['size'])
                    ->where('tarif', 'Bulanan')
                    ->first();
            }
            
            $baseDpp = $pricelist ? (float) $pricelist->harga : (float) $baseRecord->dpp;
            
            // Create missing periods
            $periodeCreated = 0;
            for ($periode = $item['max_existing'] + 1; $periode <= $item['months_needed']; $periode++) {
                // Calculate period dates
                $periodStart = $startDate->copy()->addMonthsNoOverflow($periode - 1);
                $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
                
                // Cap period end to contract end date if applicable
                if ($item['tanggal_akhir']) {
                    $containerEnd = Carbon::parse($item['tanggal_akhir']);
                    if ($periodStart->gt($containerEnd)) {
                        // Skip periods that start after contract end
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
                DaftarTagihanKontainerSewa::create([
                    'vendor' => $item['vendor'],
                    'nomor_kontainer' => $item['nomor_kontainer'],
                    'size' => $item['size'],
                    'tanggal_awal' => $item['tanggal_awal'],
                    'tanggal_akhir' => $periodEnd->format('Y-m-d'),
                    'periode' => $periode,
                    'masa' => $masaStr,
                    'tarif' => $isFullMonth ? 'Bulanan' : 'Harian',
                    'dpp' => $periodDpp,
                    'group' => $item['group'],
                    'status' => $item['status'],
                ]);
                
                $periodeCreated++;
                $totalCreated++;
            }
            
            if ($periodeCreated > 0) {
                $successCount++;
                if (($index + 1) % 50 == 0 || $index < 10) {
                    echo "✅ [{$successCount}] {$item['nomor_kontainer']}: +{$periodeCreated} periode\n";
                }
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
    
    if (!empty($errors) && count($errors) <= 20) {
        echo "⚠ Daftar Error:\n";
        foreach ($errors as $error) {
            echo "   - {$error}\n";
        }
    } elseif (count($errors) > 20) {
        echo "⚠ Terlalu banyak error ({$errorCount}). Silakan periksa log.\n";
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
