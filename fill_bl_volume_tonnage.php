<?php

/**
 * Script untuk mengisi kolom volume dan tonnage di tabel BL
 * berdasarkan data dari tabel prospek
 * 
 * Cara menjalankan:
 * php fill_bl_volume_tonnage.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Script Pengisian Volume dan Tonnage BL dari Prospek ===\n\n";

// Step 1: Update BL yang memiliki prospek_id langsung
echo "Step 1: Update BL dengan prospek_id langsung...\n";

$updated1 = DB::statement("
    UPDATE bls
    INNER JOIN prospek ON bls.prospek_id = prospek.id
    SET 
        bls.volume = COALESCE(bls.volume, prospek.total_volume),
        bls.tonnage = COALESCE(bls.tonnage, prospek.total_ton)
    WHERE bls.prospek_id IS NOT NULL
        AND (bls.volume IS NULL OR bls.tonnage IS NULL)
");

$countAfterStep1 = DB::table('bls')
    ->whereNotNull('prospek_id')
    ->where(function($q) {
        $q->whereNotNull('volume')
          ->orWhereNotNull('tonnage');
    })
    ->count();

echo "✓ Update selesai untuk BL dengan prospek_id\n";
echo "  Total BL dengan prospek_id yang sudah memiliki data: {$countAfterStep1}\n\n";

// Step 2: Update BL yang tidak memiliki prospek_id tapi bisa dicocokkan
echo "Step 2: Update BL tanpa prospek_id berdasarkan nomor_kontainer dan no_voyage...\n";

$updated2 = DB::statement("
    UPDATE bls
    INNER JOIN (
        SELECT 
            p.id as prospek_id,
            p.nomor_kontainer,
            p.no_voyage,
            p.total_volume,
            p.total_ton
        FROM prospek p
        WHERE p.nomor_kontainer IS NOT NULL 
            AND p.nomor_kontainer != ''
            AND p.no_voyage IS NOT NULL
            AND p.no_voyage != ''
            AND (p.total_volume IS NOT NULL OR p.total_ton IS NOT NULL)
    ) AS p ON bls.nomor_kontainer = p.nomor_kontainer 
        AND bls.no_voyage = p.no_voyage
    SET 
        bls.volume = COALESCE(bls.volume, p.total_volume),
        bls.tonnage = COALESCE(bls.tonnage, p.total_ton),
        bls.prospek_id = COALESCE(bls.prospek_id, p.prospek_id)
    WHERE bls.nomor_kontainer IS NOT NULL 
        AND bls.nomor_kontainer != ''
        AND bls.no_voyage IS NOT NULL
        AND bls.no_voyage != ''
        AND (bls.volume IS NULL OR bls.tonnage IS NULL)
");

echo "✓ Update selesai untuk BL berdasarkan matching kontainer dan voyage\n\n";

// Step 3: Tampilkan statistik akhir
echo "=== Statistik Akhir ===\n";

$totalBls = DB::table('bls')->count();
$blsWithVolume = DB::table('bls')->whereNotNull('volume')->count();
$blsWithTonnage = DB::table('bls')->whereNotNull('tonnage')->count();
$blsWithBoth = DB::table('bls')
    ->whereNotNull('volume')
    ->whereNotNull('tonnage')
    ->count();
$blsWithProspekId = DB::table('bls')->whereNotNull('prospek_id')->count();

echo "Total BL: {$totalBls}\n";
echo "BL dengan prospek_id: {$blsWithProspekId}\n";
echo "BL dengan volume: {$blsWithVolume} (" . round(($blsWithVolume/$totalBls)*100, 2) . "%)\n";
echo "BL dengan tonnage: {$blsWithTonnage} (" . round(($blsWithTonnage/$totalBls)*100, 2) . "%)\n";
echo "BL dengan volume & tonnage: {$blsWithBoth} (" . round(($blsWithBoth/$totalBls)*100, 2) . "%)\n";
echo "BL tanpa data: " . ($totalBls - $blsWithBoth) . " (" . round((($totalBls - $blsWithBoth)/$totalBls)*100, 2) . "%)\n";

// Step 4: Tampilkan contoh BL yang masih kosong
echo "\n=== Contoh BL yang masih kosong (5 pertama) ===\n";

$emptyBls = DB::table('bls')
    ->whereNull('volume')
    ->whereNull('tonnage')
    ->select('id', 'nomor_bl', 'nomor_kontainer', 'no_voyage', 'prospek_id')
    ->limit(5)
    ->get();

if ($emptyBls->count() > 0) {
    echo "ID\tNomor BL\t\tKontainer\tVoyage\t\tProspek ID\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($emptyBls as $bl) {
        echo "{$bl->id}\t" . 
             ($bl->nomor_bl ?: '-') . "\t\t" . 
             ($bl->nomor_kontainer ?: '-') . "\t" . 
             ($bl->no_voyage ?: '-') . "\t\t" . 
             ($bl->prospek_id ?: '-') . "\n";
    }
} else {
    echo "Semua BL sudah memiliki volume dan tonnage!\n";
}

echo "\n=== Script selesai ===\n";
