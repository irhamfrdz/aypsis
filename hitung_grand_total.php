<?php

echo "=== PERHITUNGAN GRAND TOTAL ===" . PHP_EOL;
echo PHP_EOL;

// DPP baru
$dpp = 472973;
$adjustment = 0;

echo "DPP: " . number_format($dpp, 2, '.', ',') . PHP_EOL;
echo "Adjustment: " . number_format($adjustment, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

// Hitung Adjusted DPP
$adjustedDpp = $dpp + $adjustment;
echo "Adjusted DPP: " . number_format($adjustedDpp, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

// Hitung PPN (11%)
$ppn = round($adjustedDpp * 0.11, 2);
echo "PPN (11%): " . number_format($ppn, 2, '.', ',') . PHP_EOL;

// Hitung PPH (2%)
$pph = round($adjustedDpp * 0.02, 2);
echo "PPH (2%): " . number_format($pph, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

// Hitung Grand Total
// Formula: Adjusted DPP + PPN - PPH
$grandTotal = $adjustedDpp + $ppn - $pph;

echo "=== FORMULA GRAND TOTAL ===" . PHP_EOL;
echo "Grand Total = Adjusted DPP + PPN - PPH" . PHP_EOL;
echo "Grand Total = " . number_format($adjustedDpp, 2, '.', ',') . " + " . number_format($ppn, 2, '.', ',') . " - " . number_format($pph, 2, '.', ',') . PHP_EOL;
echo "Grand Total = " . number_format($grandTotal, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo "=== PERBANDINGAN ===" . PHP_EOL;
echo "DPP Lama: 472,962.00 → Grand Total: 515,528.58" . PHP_EOL;
echo "DPP Baru: " . number_format($dpp, 2, '.', ',') . " → Grand Total: " . number_format($grandTotal, 2, '.', ',') . PHP_EOL;
echo "Selisih Grand Total: " . number_format($grandTotal - 515528.58, 2, '.', ',') . PHP_EOL;
