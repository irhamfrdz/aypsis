<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

$tagihan = DaftarTagihanKontainerSewa::find(5058);

echo "=== INVESTIGASI SUMBER DPP ===" . PHP_EOL;
echo "Kontainer: " . $tagihan->nomor_kontainer . PHP_EOL;
echo "Periode: " . $tagihan->periode . PHP_EOL;
echo PHP_EOL;

echo "=== DATA LENGKAP DARI DATABASE ===" . PHP_EOL;
$attributes = $tagihan->getAttributes();
foreach ($attributes as $key => $value) {
    if (in_array($key, ['id', 'nomor_kontainer', 'periode', 'vendor_id', 'ukuran', 
                        'dpp', 'dpp_nilai_lain', 'adjustment', 'adjustment_note',
                        'ppn', 'pph', 'grand_total', 'biaya_sewa', 'biaya_lift_on_off',
                        'biaya_lain', 'total_biaya', 'created_at', 'updated_at'])) {
        echo str_pad($key, 25) . ": " . ($value ?? 'NULL') . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== BREAKDOWN KOMPONEN BIAYA ===" . PHP_EOL;
echo "Biaya Sewa: " . number_format($tagihan->biaya_sewa ?? 0, 2, '.', ',') . PHP_EOL;
echo "Biaya Lift On/Off: " . number_format($tagihan->biaya_lift_on_off ?? 0, 2, '.', ',') . PHP_EOL;
echo "Biaya Lain: " . number_format($tagihan->biaya_lain ?? 0, 2, '.', ',') . PHP_EOL;
echo "Total Biaya: " . number_format($tagihan->total_biaya ?? 0, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;
echo "DPP: " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;
echo "DPP Nilai Lain: " . number_format($tagihan->dpp_nilai_lain ?? 0, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo PHP_EOL;
echo "=== KEMUNGKINAN SUMBER KESALAHAN ===" . PHP_EOL;
echo "1. Total Biaya seharusnya: " . number_format($tagihan->total_biaya ?? 0, 2, '.', ',') . PHP_EOL;
echo "2. DPP di database: " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;
echo "3. DPP yang seharusnya: 472,973.40" . PHP_EOL;
echo "4. Selisih: " . number_format(472973.40 - $tagihan->dpp, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

// Cek perhitungan DPP
if ($tagihan->total_biaya) {
    echo "Jika DPP = Total Biaya:" . PHP_EOL;
    echo "  Total Biaya: " . number_format($tagihan->total_biaya, 2, '.', ',') . PHP_EOL;
    echo "  DPP Database: " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;
    echo "  Cocok? " . ($tagihan->total_biaya == $tagihan->dpp ? "✅ Ya" : "❌ Tidak") . PHP_EOL;
}

echo PHP_EOL;
echo "=== ANALISIS ===" . PHP_EOL;
$sum = ($tagihan->biaya_sewa ?? 0) + ($tagihan->biaya_lift_on_off ?? 0) + ($tagihan->biaya_lain ?? 0);
echo "Sum(Sewa + Lift + Lain): " . number_format($sum, 2, '.', ',') . PHP_EOL;
echo "Total Biaya: " . number_format($tagihan->total_biaya ?? 0, 2, '.', ',') . PHP_EOL;
echo "DPP: " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;

if ($sum == $tagihan->total_biaya) {
    echo "✅ Total Biaya = Sum komponen" . PHP_EOL;
} else {
    echo "⚠️ Total Biaya ≠ Sum komponen (selisih: " . number_format(abs($sum - ($tagihan->total_biaya ?? 0)), 2, '.', ',') . ")" . PHP_EOL;
}

if ($tagihan->total_biaya == $tagihan->dpp) {
    echo "✅ DPP = Total Biaya" . PHP_EOL;
} else {
    echo "⚠️ DPP ≠ Total Biaya (selisih: " . number_format(abs($tagihan->dpp - ($tagihan->total_biaya ?? 0)), 2, '.', ',') . ")" . PHP_EOL;
}
