<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Cek Duplikat APZLU3960241 ===\n\n";

$tagihan = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'APZLU3960241')
    ->orderBy('periode')
    ->orderBy('id')
    ->get();

echo "Total records: " . $tagihan->count() . "\n\n";

$grouped = [];
foreach ($tagihan as $t) {
    $key = $t->periode;
    if (!isset($grouped[$key])) {
        $grouped[$key] = [];
    }
    $grouped[$key][] = $t;
}

foreach ($grouped as $periode => $items) {
    if (count($items) > 1) {
        echo "Periode {$periode} - DUPLIKAT (" . count($items) . " records):\n";
        foreach ($items as $item) {
            $status = $item->status_pranota ?? 'NULL';
            echo "  ID: {$item->id} | {$item->tanggal_awal} - {$item->tanggal_akhir} | {$item->masa}\n";
            echo "    Status Pranota: {$status} | Invoice ID: " . ($item->invoice_id ?? 'NULL') . "\n";
            echo "    Created: {$item->created_at}\n";
        }
        echo "\n";
    } else {
        echo "Periode {$periode} - OK (1 record)\n";
    }
}

echo "\n=== Cek Pattern Duplikat ===\n\n";

// Cek semua kontainer yang punya duplikat
$duplicates = DB::select("
    SELECT nomor_kontainer, periode, COUNT(*) as count
    FROM daftar_tagihan_kontainer_sewa
    GROUP BY nomor_kontainer, periode
    HAVING COUNT(*) > 1
    ORDER BY count DESC
    LIMIT 20
");

echo "Total kontainer dengan duplikat periode: " . count($duplicates) . "\n\n";

foreach ($duplicates as $dup) {
    echo "{$dup->nomor_kontainer} - Periode {$dup->periode}: {$dup->count} records\n";
}
