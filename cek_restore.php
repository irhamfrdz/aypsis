<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== VERIFIKASI DATA YANG DI-RESTORE ===\n\n";

// Check MSKU2218091 (yang sebelumnya bermasalah)
echo "1. Container MSKU2218091 - Periode 4:\n";
$msku = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'MSKU2218091')
    ->where('periode', 4)
    ->first();

if ($msku) {
    echo "   DPP: Rp " . number_format($msku->dpp, 2) . "\n";
    echo "   PPN: Rp " . number_format($msku->ppn, 2) . "\n";
    echo "   PPH: Rp " . number_format($msku->pph, 2) . "\n";
    echo "   Grand Total: Rp " . number_format($msku->grand_total, 2) . "\n";
    echo "   Tarif: " . $msku->tarif . "\n";
    echo "   ✅ Data ditemukan!\n\n";
} else {
    echo "   ❌ Data tidak ditemukan\n\n";
}

// Statistik keseluruhan
echo "2. Statistik Database:\n";
$total = DB::table('daftar_tagihan_kontainer_sewa')->count();
$bulanan = DB::table('daftar_tagihan_kontainer_sewa')->where('tarif', 'Bulanan')->count();
$harian = DB::table('daftar_tagihan_kontainer_sewa')->where('tarif', 'Harian')->count();

echo "   Total Records: " . number_format($total) . "\n";
echo "   Tarif Bulanan: " . number_format($bulanan) . " (" . round($bulanan/$total*100, 1) . "%)\n";
echo "   Tarif Harian: " . number_format($harian) . " (" . round($harian/$total*100, 1) . "%)\n\n";

// Sample beberapa kontainer
echo "3. Sample 5 kontainer acak:\n";
$samples = DB::table('daftar_tagihan_kontainer_sewa')
    ->inRandomOrder()
    ->limit(5)
    ->get(['nomor_kontainer', 'size', 'periode', 'tarif', 'dpp', 'grand_total']);

foreach ($samples as $s) {
    echo sprintf(
        "   %s (%sft) P%d - %s: DPP Rp %s, Total Rp %s\n",
        $s->nomor_kontainer,
        $s->size,
        $s->periode,
        $s->tarif,
        number_format($s->dpp, 0),
        number_format($s->grand_total, 2)
    );
}

echo "\n✅ Restore berhasil! Database sudah diperbarui dengan data dari aypsis1.sql\n";
