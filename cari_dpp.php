<?php

echo "=== MENCARI DPP YANG TEPAT ===" . PHP_EOL;
echo "Target Grand Total: Rp 515,541.00" . PHP_EOL;
echo "Target PPN: Rp 52,027.00" . PHP_EOL;
echo PHP_EOL;

// Formula: Grand Total = Adjusted DPP + PPN - PPH
// Grand Total = Adjusted DPP + (Adjusted DPP * 0.11) - (Adjusted DPP * 0.02)
// Grand Total = Adjusted DPP * (1 + 0.11 - 0.02)
// Grand Total = Adjusted DPP * 1.09
// Adjusted DPP = Grand Total / 1.09

$targetGrandTotal = 515541;
$estimatedDpp = $targetGrandTotal / 1.09;

echo "=== ESTIMASI AWAL ===" . PHP_EOL;
echo "DPP Estimasi (Grand Total / 1.09): " . number_format($estimatedDpp, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

// Coba berbagai nilai DPP
$testValues = [
    472972,
    472972.50,
    472972.73,
    472972.75,
    472973,
    472973.50,
    472974,
    472975,
    472976,
    472977,
    472978,
];

echo "=== TESTING BERBAGAI NILAI DPP ===" . PHP_EOL;
echo str_pad("DPP", 15) . str_pad("PPN", 15) . str_pad("PPH", 15) . str_pad("Grand Total", 15) . "Match?" . PHP_EOL;
echo str_repeat("-", 75) . PHP_EOL;

$closest = null;
$closestDiff = PHP_FLOAT_MAX;

foreach ($testValues as $dpp) {
    $adjustedDpp = $dpp;
    $ppn = round($adjustedDpp * 0.11, 2);
    $pph = round($adjustedDpp * 0.02, 2);
    $grandTotal = $adjustedDpp + $ppn - $pph;
    
    $diff = abs($grandTotal - $targetGrandTotal);
    $match = ($grandTotal == $targetGrandTotal) ? "✅ YES!" : "";
    
    if ($diff < $closestDiff) {
        $closestDiff = $diff;
        $closest = [
            'dpp' => $dpp,
            'ppn' => $ppn,
            'pph' => $pph,
            'grandTotal' => $grandTotal,
            'diff' => $diff
        ];
    }
    
    echo str_pad(number_format($dpp, 2, '.', ','), 15) . 
         str_pad(number_format($ppn, 2, '.', ','), 15) . 
         str_pad(number_format($pph, 2, '.', ','), 15) . 
         str_pad(number_format($grandTotal, 2, '.', ','), 15) . 
         $match . PHP_EOL;
}

echo PHP_EOL;
echo "=== HASIL TERDEKAT ===" . PHP_EOL;
echo "DPP: " . number_format($closest['dpp'], 2, '.', ',') . PHP_EOL;
echo "PPN (11%): " . number_format($closest['ppn'], 2, '.', ',') . PHP_EOL;
echo "PPH (2%): " . number_format($closest['pph'], 2, '.', ',') . PHP_EOL;
echo "Grand Total: " . number_format($closest['grandTotal'], 2, '.', ',') . PHP_EOL;
echo "Selisih dari target: " . number_format($closest['diff'], 2, '.', ',') . PHP_EOL;

// Coba hitung exact DPP
echo PHP_EOL;
echo "=== PERHITUNGAN EXACT (Trial & Error) ===" . PHP_EOL;

// Binary search untuk DPP yang tepat
$low = 472970;
$high = 472980;
$target = 515541;
$precision = 0.01;

while ($high - $low > $precision) {
    $mid = ($low + $high) / 2;
    $ppn = round($mid * 0.11, 2);
    $pph = round($mid * 0.02, 2);
    $grandTotal = $mid + $ppn - $pph;
    
    if (abs($grandTotal - $target) < 0.01) {
        echo "DPP Exact: " . number_format($mid, 2, '.', ',') . PHP_EOL;
        echo "PPN: " . number_format($ppn, 2, '.', ',') . PHP_EOL;
        echo "PPH: " . number_format($pph, 2, '.', ',') . PHP_EOL;
        echo "Grand Total: " . number_format($grandTotal, 2, '.', ',') . PHP_EOL;
        break;
    }
    
    if ($grandTotal < $target) {
        $low = $mid;
    } else {
        $high = $mid;
    }
}
