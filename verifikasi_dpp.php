<?php

echo "=== VERIFIKASI FINAL ===" . PHP_EOL;
echo PHP_EOL;

// DPP yang tepat untuk Grand Total 515,541
$dpp = 472973.40;
$adjustment = 0;

echo "DPP: " . number_format($dpp, 2, '.', ',') . PHP_EOL;
echo "Adjustment: " . number_format($adjustment, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

$adjustedDpp = $dpp + $adjustment;
echo "Adjusted DPP: " . number_format($adjustedDpp, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

// Hitung dengan round
$ppn = round($adjustedDpp * 0.11, 2);
$pph = round($adjustedDpp * 0.02, 2);

echo "PPN (11%): " . number_format($ppn, 2, '.', ',') . PHP_EOL;
echo "PPH (2%): " . number_format($pph, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

$grandTotal = $adjustedDpp + $ppn - $pph;

echo "=== GRAND TOTAL ===" . PHP_EOL;
echo "Formula: Adjusted DPP + PPN - PPH" . PHP_EOL;
echo "Grand Total: " . number_format($grandTotal, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo "=== RINGKASAN ===" . PHP_EOL;
echo "✅ DPP: Rp " . number_format($dpp, 2, '.', ',') . PHP_EOL;
echo "✅ PPN: Rp " . number_format($ppn, 2, '.', ',') . PHP_EOL;
echo "✅ PPH: Rp " . number_format($pph, 2, '.', ',') . PHP_EOL;
echo "✅ Grand Total: Rp " . number_format($grandTotal, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

if ($grandTotal == 515541) {
    echo "🎯 PERFECT! Grand Total tepat Rp 515,541.00" . PHP_EOL;
} else {
    echo "⚠️ Grand Total: Rp " . number_format($grandTotal, 2, '.', ',') . " (selisih: Rp " . number_format(abs($grandTotal - 515541), 2, '.', ',') . ")" . PHP_EOL;
}
echo PHP_EOL;

echo "=== PERBANDINGAN ===" . PHP_EOL;
echo "DPP Sekarang: 472,962.00 → Grand Total: 515,528.58" . PHP_EOL;
echo "DPP Seharusnya: " . number_format($dpp, 2, '.', ',') . " → Grand Total: " . number_format($grandTotal, 2, '.', ',') . PHP_EOL;
echo "Koreksi DPP: +" . number_format($dpp - 472962, 2, '.', ',') . PHP_EOL;
