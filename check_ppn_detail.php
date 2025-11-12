<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

$tagihan = DaftarTagihanKontainerSewa::find(5058);

echo "=== DETAIL PERHITUNGAN MSKU22180 PERIODE 4 ===" . PHP_EOL;
echo "ID: " . $tagihan->id . PHP_EOL;
echo "Kontainer: " . $tagihan->nomor_kontainer . PHP_EOL;
echo "Periode: " . $tagihan->periode . PHP_EOL;
echo PHP_EOL;

echo "=== NILAI DARI DATABASE ===" . PHP_EOL;
echo "DPP: " . number_format($tagihan->dpp, 2, '.', ',') . PHP_EOL;
echo "Adjustment: " . number_format($tagihan->adjustment, 2, '.', ',') . PHP_EOL;
echo "PPN (Database): " . number_format($tagihan->ppn, 2, '.', ',') . PHP_EOL;
echo "PPH (Database): " . number_format($tagihan->pph, 2, '.', ',') . PHP_EOL;
echo "Grand Total (Database): " . number_format($tagihan->grand_total, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

$dpp = floatval($tagihan->dpp);
$adjustment = floatval($tagihan->adjustment);
$adjustedDpp = $dpp + $adjustment;

echo "=== PERHITUNGAN MANUAL (TANPA ROUND) ===" . PHP_EOL;
echo "DPP: " . $dpp . PHP_EOL;
echo "Adjustment: " . $adjustment . PHP_EOL;
echo "Adjusted DPP: " . $adjustedDpp . PHP_EOL;
echo PHP_EOL;

$ppn_no_round = $adjustedDpp * 0.11;
$pph_no_round = $adjustedDpp * 0.02;

echo "PPN (11% tanpa round): " . number_format($ppn_no_round, 10, '.', ',') . PHP_EOL;
echo "PPH (2% tanpa round): " . number_format($pph_no_round, 10, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo "=== PERHITUNGAN MANUAL (DENGAN ROUND) ===" . PHP_EOL;
$ppn_round = round($adjustedDpp * 0.11, 2);
$pph_round = round($adjustedDpp * 0.02, 2);

echo "PPN (11% dengan round): " . number_format($ppn_round, 2, '.', ',') . PHP_EOL;
echo "PPH (2% dengan round): " . number_format($pph_round, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo "=== ANALISIS ===" . PHP_EOL;
echo "PPN Database vs Tanpa Round: " . ($tagihan->ppn == $ppn_no_round ? "✅ SAMA" : "❌ BEDA") . PHP_EOL;
echo "PPN Database vs Dengan Round: " . ($tagihan->ppn == $ppn_round ? "✅ SAMA" : "❌ BEDA") . PHP_EOL;
echo "Selisih (Database - Round): " . number_format($tagihan->ppn - $ppn_round, 10, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo "=== CONTOH PERHITUNGAN DPP YANG BERBEDA ===" . PHP_EOL;
echo "Jika DPP = 472,972 (bukan 472,962):" . PHP_EOL;
$dpp_alternative = 472972;
$ppn_alt = round($dpp_alternative * 0.11, 2);
echo "  PPN = " . number_format($ppn_alt, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo "Jika DPP = 472,790.91:" . PHP_EOL;
$dpp_alternative2 = 472790.91;
$ppn_alt2 = round($dpp_alternative2 * 0.11, 2);
echo "  PPN = " . number_format($ppn_alt2, 2, '.', ',') . PHP_EOL;
echo PHP_EOL;

echo "Untuk mendapat PPN = 52,027, DPP harus:" . PHP_EOL;
$target_ppn = 52027;
$required_dpp = $target_ppn / 0.11;
echo "  DPP = " . number_format($required_dpp, 2, '.', ',') . PHP_EOL;
echo "  Cek: " . number_format(round($required_dpp * 0.11, 2), 2, '.', ',') . PHP_EOL;
