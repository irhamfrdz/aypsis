<?php
// verifikasi_pembulatan.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFIKASI PEMBULATAN GRAND TOTAL ===\n\n";

// Check MSKU2218091 P4
$msku = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'MSKU2218091')
    ->where('periode', 4)
    ->first();

if ($msku) {
    echo "MSKU2218091 - Periode 4:\n";
    echo "DPP: Rp " . number_format($msku->dpp, 2, ',', '.') . "\n";
    echo "PPN: Rp " . number_format($msku->ppn, 2, ',', '.') . "\n";
    echo "PPH: Rp " . number_format($msku->pph, 2, ',', '.') . "\n";
    echo "Grand Total: Rp " . number_format($msku->grand_total, 0, ',', '.') . "\n";
    
    // Check if it's rounded
    $isRounded = ($msku->grand_total == round($msku->grand_total));
    echo "Status: " . ($isRounded ? "✓ SUDAH DIBULATKAN" : "✗ BELUM DIBULATKAN") . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Check statistics
$stats = DB::select("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN grand_total = ROUND(grand_total) THEN 1 ELSE 0 END) as rounded,
        SUM(CASE WHEN grand_total != ROUND(grand_total) THEN 1 ELSE 0 END) as not_rounded
    FROM daftar_tagihan_kontainer_sewa
")[0];

echo "STATISTIK PEMBULATAN:\n";
echo "Total Records: {$stats->total}\n";
echo "Sudah Dibulatkan: {$stats->rounded}\n";
echo "Belum Dibulatkan: {$stats->not_rounded}\n";

// Sample random records
echo "\n" . str_repeat("=", 80) . "\n";
echo "\nSAMPLE RANDOM (5 records):\n\n";

$samples = DB::table('daftar_tagihan_kontainer_sewa')
    ->inRandomOrder()
    ->limit(5)
    ->get();

foreach ($samples as $item) {
    $isRounded = ($item->grand_total == round($item->grand_total));
    echo sprintf(
        "ID: %s | %s P%s | Grand Total: Rp %s %s\n",
        $item->id,
        $item->nomor_kontainer,
        $item->periode,
        number_format($item->grand_total, 2, ',', '.'),
        $isRounded ? "✓" : "✗"
    );
}

echo "\nDone!\n";
