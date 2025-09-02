<?php

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Get pranota PTK12509000001
$pranota = DB::table('pranotalist')->where('no_invoice', 'PTK12509000001')->first();

if (!$pranota) {
    echo "Pranota PTK12509000001 tidak ditemukan!\n";
    exit;
}

echo "=== DEBUG TOTAL AMOUNT ===\n";
echo "Pranota No: {$pranota->no_invoice}\n";
echo "Total Amount di database: Rp " . number_format($pranota->total_amount, 2) . "\n";
echo "Tagihan IDs: {$pranota->tagihan_ids}\n\n";

// Decode tagihan_ids
$tagihanIds = json_decode($pranota->tagihan_ids, true);
if (empty($tagihanIds)) {
    echo "Tagihan IDs kosong!\n";
    exit;
}

echo "=== DETAIL TAGIHAN ===\n";
$totalCalculated = 0;

foreach ($tagihanIds as $tagihanId) {
    $tagihan = DB::table('daftar_tagihan_kontainer_sewa')->where('id', $tagihanId)->first();

    if ($tagihan) {
        echo "ID: {$tagihan->id}\n";
        echo "No Kontainer: {$tagihan->nomor_kontainer}\n";
        echo "Size: {$tagihan->size}\n";
        echo "Periode: {$tagihan->periode}\n";
        echo "Tarif: {$tagihan->tarif}\n";
        echo "Tarif Nominal: Rp " . number_format(floatval($tagihan->tarif_nominal), 2) . "\n";
        echo "DPP: Rp " . number_format(floatval($tagihan->dpp), 2) . "\n";
        echo "Adjustment: Rp " . number_format(floatval($tagihan->adjustment), 2) . "\n";
        echo "PPN: Rp " . number_format(floatval($tagihan->ppn), 2) . "\n";
        echo "PPH: Rp " . number_format(floatval($tagihan->pph), 2) . "\n";
        echo "Grand Total: Rp " . number_format(floatval($tagihan->grand_total), 2) . "\n";
        echo "---\n";

        $totalCalculated += floatval($tagihan->grand_total);
    } else {
        echo "Tagihan ID {$tagihanId} tidak ditemukan!\n";
    }
}

echo "\n=== RINGKASAN ===\n";
echo "Total Amount di database: Rp " . number_format($pranota->total_amount, 2) . "\n";
echo "Total yang dihitung: Rp " . number_format($totalCalculated, 2) . "\n";
echo "Selisih: Rp " . number_format($totalCalculated - $pranota->total_amount, 2) . "\n";

if ($totalCalculated != $pranota->total_amount) {
    echo "\n❌ TOTAL AMOUNT TIDAK SESUAI!\n";
    echo "Perlu update total amount di database.\n";
} else {
    echo "\n✅ Total amount sudah benar.\n";
}
