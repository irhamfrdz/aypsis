<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== Debug Grand Total Calculation ===\n\n";

// Get the tagihan
$tagihan = DaftarTagihanKontainerSewa::find(1199);

if (!$tagihan) {
    echo "Tagihan not found!\n";
    exit;
}

echo "Tagihan ID: {$tagihan->id}\n";
echo "DPP: Rp " . number_format($tagihan->dpp ?? 0, 2, ',', '.') . "\n";
echo "Adjustment: Rp " . number_format($tagihan->adjustment ?? 0, 2, ',', '.') . "\n";
echo "PPN Persen (raw): "; var_dump($tagihan->ppn_persen);
echo "PPN Nilai (raw): "; var_dump($tagihan->ppn_nilai);
echo "PPH Persen (raw): "; var_dump($tagihan->pph_persen);
echo "PPH Nilai (raw): "; var_dump($tagihan->pph_nilai);
echo "Grand Total (DB): Rp " . number_format($tagihan->grand_total ?? 0, 2, ',', '.') . "\n\n";

// Let's calculate step by step like the front-end would
$dpp = $tagihan->dpp ?? 0;
$adjustment = $tagihan->adjustment ?? 0;

// Calculate PPN based on (DPP + Adjustment)
$ppnBase = $dpp + $adjustment;
$ppnPersen = $tagihan->ppn_persen ?? 0;
$ppnNilai = ($ppnBase * $ppnPersen) / 100;

// Calculate PPH based on DPP only (not including adjustment)
$pphPersen = $tagihan->pph_persen ?? 0;
$pphNilai = ($dpp * $pphPersen) / 100;

// Final Grand Total
$grandTotal = $dpp + $adjustment + $ppnNilai - $pphNilai;

echo "=== CORRECT CALCULATION ===\n";
echo "DPP: Rp " . number_format($dpp, 2, ',', '.') . "\n";
echo "Adjustment: Rp " . number_format($adjustment, 2, ',', '.') . "\n";
echo "PPN Base (DPP + Adj): Rp " . number_format($ppnBase, 2, ',', '.') . "\n";
echo "PPN ({$ppnPersen}%): Rp " . number_format($ppnNilai, 2, ',', '.') . "\n";
echo "PPH ({$pphPersen}% dari DPP): Rp " . number_format($pphNilai, 2, ',', '.') . "\n";
echo "Grand Total: Rp " . number_format($grandTotal, 2, ',', '.') . "\n\n";

echo "=== COMPARISON ===\n";
echo "DB grand_total: Rp " . number_format($tagihan->grand_total ?? 0, 2, ',', '.') . "\n";
echo "Calculated total: Rp " . number_format($grandTotal, 2, ',', '.') . "\n";
echo "Match: " . (abs(($tagihan->grand_total ?? 0) - $grandTotal) < 0.01 ? "YES" : "NO") . "\n";

echo "\n=== Analysis Complete ===\n";
