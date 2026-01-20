<?php
/**
 * Script untuk membuat periode berikutnya untuk kontainer
 * yang tanggal_awal-nya sudah lama tapi hanya punya 1 record untuk kontainer_nomor tersebut
 * 
 * Jalankan dengan: php create_missing_periode.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Script Buat Periode untuk Kontainer Aktif\n";
echo "Tanggal: " . now()->format('d M Y H:i:s') . "\n";
echo "===========================================\n\n";

// Mode 1: Kontainer yang hanya punya 1 record TOTAL tapi durasi > 30 hari
echo "🔍 Mode 1: Kontainer dengan 1 record tapi durasi > 30 hari\n";

$singleRecordContainers = DaftarTagihanKontainerSewa::select('nomor_kontainer')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('nomor_kontainer')
    ->havingRaw('COUNT(*) = 1')
    ->pluck('nomor_kontainer');

$needsPeriodesMode1 = collect();

foreach ($singleRecordContainers as $nomorKontainer) {
    $record = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->first();
    
    if (!$record || !$record->tanggal_awal) continue;
    
    try {
        $startDate = Carbon::parse($record->tanggal_awal);
        
        // Skip jika tahun aneh (< 2000)
        if ($startDate->year < 2000) continue;
        
        // Determine end date
        if ($record->tanggal_akhir) {
            $endDate = Carbon::parse($record->tanggal_akhir);
            if ($endDate->year < 2000) continue;
        } else {
            $endDate = Carbon::now();
        }
        
        // Calculate months
        $monthsNeeded = max(1, intval($startDate->diffInMonths($endDate)) + 1);
        
        // If more than 1 month needed
        if ($monthsNeeded > 1) {
            $needsPeriodesMode1->push([
                'record' => $record,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'months_needed' => $monthsNeeded,
                'current_max' => 1
            ]);
        }
    } catch (\Exception $e) {
        continue;
    }
}

echo "   Ditemukan: " . $needsPeriodesMode1->count() . " kontainer\n\n";

// Mode 2: Kontainer yang max periodenya < months sejak tanggal_awal paling awal
echo "🔍 Mode 2: Kontainer yang periode max < bulan sejak awal kontrak\n";

$allContainers = DaftarTagihanKontainerSewa::selectRaw('
    nomor_kontainer, 
    vendor, 
    size,
    MIN(tanggal_awal) as earliest_start,
    MAX(tanggal_akhir) as latest_end,
    MAX(periode) as max_periode
')
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->groupBy('nomor_kontainer', 'vendor', 'size')
    ->get();

$needsPeriodesMode2 = collect();

foreach ($allContainers as $container) {
    if (!$container->earliest_start) continue;
    
    try {
        $startDate = Carbon::parse($container->earliest_start);
        if ($startDate->year < 2000) continue;
        
        // Use latest_end if exists, otherwise now
        if ($container->latest_end) {
            $endDate = Carbon::parse($container->latest_end);
        } else {
            $endDate = Carbon::now();
        }
        
        $monthsNeeded = max(1, intval($startDate->diffInMonths($endDate)) + 1);
        $currentMax = (int) $container->max_periode;
        
        if ($monthsNeeded > $currentMax) {
            $needsPeriodesMode2->push([
                'nomor_kontainer' => $container->nomor_kontainer,
                'vendor' => $container->vendor,
                'size' => $container->size,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'months_needed' => $monthsNeeded,
                'current_max' => $currentMax,
                'periods_to_create' => $monthsNeeded - $currentMax
            ]);
        }
    } catch (\Exception $e) {
        continue;
    }
}

echo "   Ditemukan: " . $needsPeriodesMode2->count() . " kontainer\n\n";

// Show details
if ($needsPeriodesMode2->count() > 0) {
    echo "📋 Detail 20 kontainer pertama (Mode 2):\n";
    echo str_repeat("-", 110) . "\n";
    printf("%-18s %-10s %-6s %-12s %-12s %-8s %-8s %-8s\n", 
        "Nomor", "Vendor", "Size", "Earliest", "Latest", "MaxPer", "Target", "ToAdd");
    echo str_repeat("-", 110) . "\n";
    
    foreach ($needsPeriodesMode2->take(20) as $item) {
        printf("%-18s %-10s %-6s %-12s %-12s %-8d %-8d %-8d\n",
            $item['nomor_kontainer'],
            $item['vendor'] ?? '-',
            $item['size'] ?? '-',
            $item['start_date']->format('d/m/Y'),
            $item['end_date']->format('d/m/Y'),
            $item['current_max'],
            $item['months_needed'],
            $item['periods_to_create']
        );
    }
    
    echo str_repeat("-", 110) . "\n\n";
    
    $totalToCreate = $needsPeriodesMode2->sum('periods_to_create');
    echo "📊 Total periode yang akan dibuat: {$totalToCreate}\n\n";
    
    echo "⚠ Lanjutkan? (y/n): ";
    $confirm = trim(fgets(STDIN));
    
    if (strtolower($confirm) === 'y') {
        echo "\n===========================================\n";
        echo "Memproses...\n";
        echo "===========================================\n\n";
        
        $totalCreated = 0;
        $errorCount = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($needsPeriodesMode2 as $item) {
                // Get base record (highest periode)
                $baseRecord = DaftarTagihanKontainerSewa::where('nomor_kontainer', $item['nomor_kontainer'])
                    ->where('vendor', $item['vendor'])
                    ->orderBy('periode', 'desc')
                    ->first();
                
                if (!$baseRecord) continue;
                
                // Get pricelist
                $pricelist = MasterPricelistSewaKontainer::where('vendor', $item['vendor'])
                    ->where('ukuran_kontainer', $item['size'])
                    ->where('tarif', 'Bulanan')
                    ->first();
                
                $baseDpp = $pricelist ? (float) $pricelist->harga : (float) $baseRecord->dpp;
                
                // Create missing periods
                for ($periode = $item['current_max'] + 1; $periode <= $item['months_needed']; $periode++) {
                    // Check if already exists
                    $exists = DaftarTagihanKontainerSewa::where('nomor_kontainer', $item['nomor_kontainer'])
                        ->where('vendor', $item['vendor'])
                        ->where('periode', $periode)
                        ->exists();
                    
                    if ($exists) continue;
                    
                    // Calculate period dates based on earliest start
                    $periodStart = $item['start_date']->copy()->addMonthsNoOverflow($periode - 1);
                    $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
                    
                    // Cap end date if applicable
                    if ($item['end_date'] && $periodEnd->gt($item['end_date'])) {
                        $periodEnd = $item['end_date']->copy();
                    }
                    
                    // Skip if period start is after end
                    if ($periodStart->gt($item['end_date'])) continue;
                    
                    // Calculate DPP
                    $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
                    $daysInMonth = $periodStart->daysInMonth;
                    $isFullMonth = $daysInPeriod >= $daysInMonth;
                    $periodDpp = $isFullMonth ? $baseDpp : round($baseDpp * ($daysInPeriod / $daysInMonth), 2);
                    
                    // Generate masa
                    $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 
                               7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
                    $masa = $periodStart->format('j') . ' ' . $months[$periodStart->month] . ' ' . $periodStart->year 
                          . ' - ' . $periodEnd->format('j') . ' ' . $months[$periodEnd->month] . ' ' . $periodEnd->year;
                    
                    DaftarTagihanKontainerSewa::create([
                        'vendor' => $item['vendor'],
                        'nomor_kontainer' => $item['nomor_kontainer'],
                        'size' => $item['size'],
                        'tanggal_awal' => $item['start_date']->format('Y-m-d'),
                        'tanggal_akhir' => $periodEnd->format('Y-m-d'),
                        'periode' => $periode,
                        'masa' => $masa,
                        'tarif' => $isFullMonth ? 'Bulanan' : 'Harian',
                        'dpp' => $periodDpp,
                        'group' => $baseRecord->group,
                        'status' => $baseRecord->status,
                    ]);
                    
                    $totalCreated++;
                }
            }
            
            DB::commit();
            
            echo "\n✅ Selesai!\n";
            echo "   - Total periode dibuat: {$totalCreated}\n";
            echo "   - Error: {$errorCount}\n";
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Dibatalkan.\n";
    }
} else {
    echo "✅ Tidak ada kontainer yang perlu diproses!\n";
}

echo "\n===========================================\n";
echo "Script selesai.\n";
echo "===========================================\n";
