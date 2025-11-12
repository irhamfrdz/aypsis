<?php
// verifikasi_zona20ft_harian.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFIKASI UPDATE ZONA 20FT HARIAN ===\n\n";

// Get sample records
$samples = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('size', '20')
    ->whereRaw("LOWER(IFNULL(tarif, '')) = 'harian'")
    ->orderBy('id')
    ->limit(5)
    ->get();

echo "SAMPLE RECORDS (First 5):\n";
echo str_repeat("-", 120) . "\n";
echo sprintf("%-8s %-15s %-8s %-12s %-12s %-12s %-12s %-15s\n", 
    "ID", "Nomor Kont", "Masa", "DPP", "PPN", "PPH", "Grand Total", "Expected DPP");
echo str_repeat("-", 120) . "\n";

foreach ($samples as $item) {
    // Calculate days from dates
    $start = \Carbon\Carbon::parse($item->tanggal_awal);
    $end = \Carbon\Carbon::parse($item->tanggal_akhir);
    $masa = $start->diffInDays($end) + 1;
    
    $expectedDpp = 22522.53 * $masa;
    echo sprintf("%-8s %-15s %-8s %-12s %-12s %-12s %-12s %-15s\n",
        $item->id,
        $item->nomor_kontainer,
        $masa,
        number_format((float)$item->dpp, 2, '.', ','),
        number_format((float)$item->ppn, 2, '.', ','),
        number_format((float)$item->pph, 2, '.', ','),
        number_format((float)$item->grand_total, 2, '.', ','),
        number_format($expectedDpp, 2, '.', ',')
    );
}

echo "\n";

// Statistics
$stats = DB::table('daftar_tagihan_kontainer_sewa')
    ->selectRaw('
        COUNT(*) as total,
        MIN(masa) as min_masa,
        MAX(masa) as max_masa,
        AVG(masa) as avg_masa,
        SUM(dpp) as total_dpp,
        SUM(ppn) as total_ppn,
        SUM(pph) as total_pph,
        SUM(grand_total) as total_grand
    ')
    ->where('size', '20')
    ->whereRaw("LOWER(IFNULL(tarif, '')) = 'harian'")
    ->first();

echo "STATISTICS:\n";
echo "Total Records: {$stats->total}\n";
echo "Masa Range: {$stats->min_masa} - {$stats->max_masa} days (avg: " . round($stats->avg_masa, 2) . ")\n";
echo "Total DPP: Rp " . number_format($stats->total_dpp, 2, ',', '.') . "\n";
echo "Total PPN: Rp " . number_format($stats->total_ppn, 2, ',', '.') . "\n";
echo "Total PPH: Rp " . number_format($stats->total_pph, 2, ',', '.') . "\n";
echo "Total Grand: Rp " . number_format($stats->total_grand, 2, ',', '.') . "\n";

echo "\n";

// Verify calculation accuracy
$verification = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('size', '20')
    ->whereRaw("LOWER(IFNULL(tarif, '')) = 'harian'")
    ->get();

$accurate = 0;
$inaccurate = 0;

foreach ($verification as $item) {
    // Calculate days from tanggal_awal and tanggal_akhir (same logic as update script)
    if (empty($item->tanggal_awal) || empty($item->tanggal_akhir)) {
        continue;
    }
    $start = \Carbon\Carbon::parse($item->tanggal_awal);
    $end = \Carbon\Carbon::parse($item->tanggal_akhir);
    $masa = $start->diffInDays($end) + 1; // +1 untuk inclusive
    
    $expectedDpp = round(22522.53 * $masa, 2);
    $expectedPpn = round($expectedDpp * 0.11, 2);
    $expectedPph = round($expectedDpp * 0.02, 2);
    $expectedGrand = round($expectedDpp + $expectedPpn - $expectedPph, 2);
    
    $actualDpp = (float) $item->dpp;
    $actualPpn = (float) $item->ppn;
    $actualPph = (float) $item->pph;
    $actualGrand = (float) $item->grand_total;
    
    if (abs($actualDpp - $expectedDpp) < 0.01 &&
        abs($actualPpn - $expectedPpn) < 0.01 &&
        abs($actualPph - $expectedPph) < 0.01 &&
        abs($actualGrand - $expectedGrand) < 0.01) {
        $accurate++;
    } else {
        $inaccurate++;
        echo "MISMATCH: ID {$item->id} - Expected DPP: {$expectedDpp}, Actual: {$actualDpp}\n";
    }
}

echo "\nACCURACY CHECK:\n";
echo "Accurate: $accurate\n";
echo "Inaccurate: $inaccurate\n";

echo "\nDone!\n";
