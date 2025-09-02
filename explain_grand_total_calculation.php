<?php

echo "=== PENJELASAN PERHITUNGAN GRAND TOTAL ===\n\n";

// Simulasi data dari database
$originalDpp = 22523;          // DPP asli dari database
$adjustment = 10000;           // Adjustment yang ditambahkan
$dppNilaiLain = 20646;        // DPP Nilai Lain dari database
$ppnRate = 0.11;              // PPN 11%
$pphRate = 0.02;              // PPH 2%

echo "📊 DATA AWAL:\n";
echo "   • DPP Asli: Rp " . number_format($originalDpp, 0, '.', ',') . "\n";
echo "   • Adjustment: +Rp " . number_format($adjustment, 0, '.', ',') . "\n";
echo "   • DPP Nilai Lain: Rp " . number_format($dppNilaiLain, 0, '.', ',') . "\n";
echo "   • Rate PPN: " . ($ppnRate * 100) . "%\n";
echo "   • Rate PPH: " . ($pphRate * 100) . "%\n\n";

echo "🔄 LANGKAH PERHITUNGAN:\n\n";

// Step 1: Hitung Adjusted DPP
$adjustedDpp = $originalDpp + $adjustment;
echo "1️⃣ ADJUSTED DPP:\n";
echo "   Formula: DPP Asli + Adjustment\n";
echo "   Perhitungan: Rp " . number_format($originalDpp, 0, '.', ',') . " + Rp " . number_format($adjustment, 0, '.', ',') . "\n";
echo "   Hasil: Rp " . number_format($adjustedDpp, 0, '.', ',') . "\n\n";

// Step 2: Hitung PPN
$calculatedPpn = $adjustedDpp * $ppnRate;
echo "2️⃣ PPN (Pajak Pertambahan Nilai):\n";
echo "   Formula: Adjusted DPP × 11%\n";
echo "   Perhitungan: Rp " . number_format($adjustedDpp, 0, '.', ',') . " × " . ($ppnRate * 100) . "%\n";
echo "   Hasil: Rp " . number_format($calculatedPpn, 0, '.', ',') . "\n\n";

// Step 3: Hitung PPH
$calculatedPph = $adjustedDpp * $pphRate;
echo "3️⃣ PPH (Pajak Penghasilan):\n";
echo "   Formula: Adjusted DPP × 2%\n";
echo "   Perhitungan: Rp " . number_format($adjustedDpp, 0, '.', ',') . " × " . ($pphRate * 100) . "%\n";
echo "   Hasil: Rp " . number_format($calculatedPph, 0, '.', ',') . "\n\n";

// Step 4: Hitung Grand Total
$newGrandTotal = $adjustedDpp + $dppNilaiLain + $calculatedPpn - $calculatedPph;
echo "4️⃣ GRAND TOTAL:\n";
echo "   Formula: Adjusted DPP + DPP Nilai Lain + PPN - PPH\n";
echo "   Perhitungan:\n";
echo "     Rp " . number_format($adjustedDpp, 0, '.', ',') . " (Adjusted DPP)\n";
echo "   + Rp " . number_format($dppNilaiLain, 0, '.', ',') . " (DPP Nilai Lain)\n";
echo "   + Rp " . number_format($calculatedPpn, 0, '.', ',') . " (PPN)\n";
echo "   - Rp " . number_format($calculatedPph, 0, '.', ',') . " (PPH)\n";
echo "   ────────────────────────────\n";
echo "   = Rp " . number_format($newGrandTotal, 0, '.', ',') . "\n\n";

echo "📋 RINGKASAN KOMPONEN:\n\n";

$components = [
    'Adjusted DPP' => $adjustedDpp,
    'DPP Nilai Lain' => $dppNilaiLain,
    'PPN (+)' => $calculatedPpn,
    'PPH (-)' => -$calculatedPph,
    'GRAND TOTAL' => $newGrandTotal
];

foreach ($components as $name => $value) {
    $sign = $value < 0 ? '-' : '+';
    $displayValue = abs($value);

    if ($name === 'GRAND TOTAL') {
        echo "   🏆 {$name}: Rp " . number_format($displayValue, 0, '.', ',') . "\n";
    } elseif ($value < 0) {
        echo "   🔴 {$name}: -{$sign}Rp " . number_format($displayValue, 0, '.', ',') . "\n";
    } else {
        echo "   🟢 {$name}: Rp " . number_format($displayValue, 0, '.', ',') . "\n";
    }
}

echo "\n";

// Bandingkan dengan perhitungan tanpa adjustment
echo "🔍 PERBANDINGAN DENGAN/TANPA ADJUSTMENT:\n\n";

// Tanpa adjustment
$originalPpn = $originalDpp * $ppnRate;
$originalPph = $originalDpp * $pphRate;
$originalGrandTotal = $originalDpp + $dppNilaiLain + $originalPpn - $originalPph;

echo "📊 TANPA ADJUSTMENT:\n";
echo "   • DPP: Rp " . number_format($originalDpp, 0, '.', ',') . "\n";
echo "   • PPN: Rp " . number_format($originalPpn, 0, '.', ',') . " (dari DPP asli)\n";
echo "   • PPH: Rp " . number_format($originalPph, 0, '.', ',') . " (dari DPP asli)\n";
echo "   • Grand Total: Rp " . number_format($originalGrandTotal, 0, '.', ',') . "\n\n";

echo "📊 DENGAN ADJUSTMENT (+Rp " . number_format($adjustment, 0, '.', ',') . "):\n";
echo "   • DPP: Rp " . number_format($adjustedDpp, 0, '.', ',') . "\n";
echo "   • PPN: Rp " . number_format($calculatedPpn, 0, '.', ',') . " (dari DPP yang disesuaikan)\n";
echo "   • PPH: Rp " . number_format($calculatedPph, 0, '.', ',') . " (dari DPP yang disesuaikan)\n";
echo "   • Grand Total: Rp " . number_format($newGrandTotal, 0, '.', ',') . "\n\n";

// Hitung selisih
$totalDifference = $newGrandTotal - $originalGrandTotal;
echo "💰 DAMPAK ADJUSTMENT:\n";
echo "   Selisih Grand Total: ";
if ($totalDifference > 0) {
    echo "+Rp " . number_format($totalDifference, 0, '.', ',') . " (naik)\n";
} elseif ($totalDifference < 0) {
    echo "-Rp " . number_format(abs($totalDifference), 0, '.', ',') . " (turun)\n";
} else {
    echo "Rp 0 (tidak berubah)\n";
}

echo "\n";
echo "✅ KESIMPULAN:\n";
echo "   Adjustment tidak hanya mempengaruhi DPP, tetapi juga:\n";
echo "   • PPN (karena dihitung dari DPP yang disesuaikan)\n";
echo "   • PPH (karena dihitung dari DPP yang disesuaikan)\n";
echo "   • Grand Total (sebagai hasil akhir dari semua komponen)\n\n";

echo "📝 FORMULA AKHIR:\n";
echo "   Grand Total = (DPP + Adjustment) + DPP_Nilai_Lain + PPN - PPH\n";
echo "   Dimana:\n";
echo "   • PPN = (DPP + Adjustment) × 11%\n";
echo "   • PPH = (DPP + Adjustment) × 2%\n";

echo "\n=== PENJELASAN SELESAI ===\n";

?>
