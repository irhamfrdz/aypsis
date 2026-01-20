<?php
/**
 * Script untuk memperbaiki kontainer yang tanggal_awal = tanggal_akhir
 * dan membuat periode lanjutan sampai sekarang
 * 
 * Jalankan dengan: php fix_same_date_containers.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Script Fix Kontainer dengan Tanggal Sama\n";
echo "Tanggal: " . now()->format('d M Y H:i:s') . "\n";
echo "===========================================\n\n";

// Find containers where tanggal_awal = tanggal_akhir AND only have 1 period
$problemContainers = DaftarTagihanKontainerSewa::whereColumn('tanggal_awal', 'tanggal_akhir')
    ->where('periode', 1)
    ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
    ->get();

echo "📦 Kontainer dengan tanggal_awal = tanggal_akhir (periode 1): " . $problemContainers->count() . "\n\n";

if ($problemContainers->isEmpty()) {
    echo "✅ Tidak ada kontainer yang perlu diperbaiki!\n";
    exit(0);
}

echo "📋 Detail kontainer:\n";
echo str_repeat("-", 120) . "\n";
printf("%-18s %-10s %-6s %-15s %-15s %-10s %-12s\n", 
    "Nomor", "Vendor", "Size", "Tgl Awal", "Status", "DPP", "Grup");
echo str_repeat("-", 120) . "\n";

foreach ($problemContainers as $record) {
    printf("%-18s %-10s %-6s %-15s %-15s %-10s %-12s\n",
        $record->nomor_kontainer,
        $record->vendor ?? '-',
        $record->size ?? '-',
        Carbon::parse($record->tanggal_awal)->format('d/m/Y'),
        $record->status ?? '-',
        number_format($record->dpp ?? 0, 0, ',', '.'),
        $record->group ?? '-'
    );
}

echo str_repeat("-", 120) . "\n\n";

// Calculate how many periods should be created
$currentDate = Carbon::now();
$totalPeriodsToCreate = 0;

$toProcess = collect();

foreach ($problemContainers as $record) {
    $startDate = Carbon::parse($record->tanggal_awal);
    
    // Skip if year is wrong
    if ($startDate->year < 2000) continue;
    
    // Calculate months from start to now
    $monthsToNow = max(1, intval($startDate->diffInMonths($currentDate)) + 1);
    
    // We'll create periods from 1 to monthsToNow (replacing the existing period 1 data with correct end dates)
    $periodsNeeded = $monthsToNow;
    
    $toProcess->push([
        'record' => $record,
        'start_date' => $startDate,
        'periods_needed' => $periodsNeeded
    ]);
    
    // -1 because period 1 already exists (we'll just update it)
    $totalPeriodsToCreate += ($periodsNeeded - 1);
}

echo "📊 Statistik:\n";
echo "   - Kontainer untuk diproses: " . $toProcess->count() . "\n";
echo "   - Total periode baru yang akan dibuat: {$totalPeriodsToCreate}\n\n";

echo "Proses ini akan:\n";
echo "   1. Update tanggal_akhir periode 1 menjadi tanggal_awal + 1 bulan - 1 hari\n";
echo "   2. Membuat periode 2, 3, dst sampai sekarang\n\n";

echo "⚠ Lanjutkan? (y/n): ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) !== 'y') {
    echo "❌ Dibatalkan.\n";
    exit(0);
}

echo "\n===========================================\n";
echo "Memproses...\n";
echo "===========================================\n\n";

$successCount = 0;
$errorCount = 0;
$periodsCreated = 0;

DB::beginTransaction();

try {
    foreach ($toProcess as $item) {
        $record = $item['record'];
        $startDate = $item['start_date'];
        $periodsNeeded = $item['periods_needed'];
        
        // Get pricelist
        $pricelist = MasterPricelistSewaKontainer::where('vendor', $record->vendor)
            ->where('ukuran_kontainer', $record->size)
            ->where('tarif', 'Bulanan')
            ->first();
        
        $baseDpp = $pricelist ? (float) $pricelist->harga : (float) $record->dpp;
        
        // Update period 1 with correct end date
        $period1End = $startDate->copy()->addMonthsNoOverflow(1)->subDay();
        
        $record->tanggal_akhir = $period1End->format('Y-m-d');
        $record->masa = $startDate->format('j M Y') . ' - ' . $period1End->format('j M Y');
        $record->dpp = $baseDpp;
        $record->save();
        
        // Create periods 2 to periodsNeeded
        for ($periode = 2; $periode <= $periodsNeeded; $periode++) {
            $periodStart = $startDate->copy()->addMonthsNoOverflow($periode - 1);
            $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
            
            // Calculate DPP
            $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;
            $daysInMonth = $periodStart->daysInMonth;
            $isFullMonth = $daysInPeriod >= $daysInMonth;
            $periodDpp = $isFullMonth ? $baseDpp : round($baseDpp * ($daysInPeriod / $daysInMonth), 2);
            
            // Generate masa
            $masa = $periodStart->format('j M Y') . ' - ' . $periodEnd->format('j M Y');
            
            // Check if already exists
            $exists = DaftarTagihanKontainerSewa::where('nomor_kontainer', $record->nomor_kontainer)
                ->where('vendor', $record->vendor)
                ->where('periode', $periode)
                ->exists();
            
            if (!$exists) {
                DaftarTagihanKontainerSewa::create([
                    'vendor' => $record->vendor,
                    'nomor_kontainer' => $record->nomor_kontainer,
                    'size' => $record->size,
                    'tanggal_awal' => $record->tanggal_awal,
                    'tanggal_akhir' => $periodEnd->format('Y-m-d'),
                    'periode' => $periode,
                    'masa' => $masa,
                    'tarif' => $isFullMonth ? 'Bulanan' : 'Harian',
                    'dpp' => $periodDpp,
                    'group' => $record->group,
                    'status' => $record->status,
                ]);
                
                $periodsCreated++;
            }
        }
        
        $successCount++;
        echo "✅ {$record->nomor_kontainer}: updated + " . ($periodsNeeded - 1) . " periode baru\n";
    }
    
    DB::commit();
    
    echo "\n===========================================\n";
    echo "✅ PROSES SELESAI!\n";
    echo "===========================================\n";
    echo "   - Kontainer diproses: {$successCount}\n";
    echo "   - Total periode baru dibuat: {$periodsCreated}\n";
    echo "   - Error: {$errorCount}\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n===========================================\n";
echo "Script selesai!\n";
echo "===========================================\n";
