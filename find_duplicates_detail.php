<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Dari screenshot terlihat APZLU3960241 dengan vendor ZONA (ZS3)
// Coba cari yang mirip
echo "=== Cari kontainer APZLU atau APZU dengan ZONA ===\n\n";

$tagihan = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'LIKE', 'AP%U%960241')
    ->orWhere(function($q) {
        $q->where('vendor', 'ZS3')
          ->where('nomor_kontainer', 'LIKE', 'AP%');
    })
    ->orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->get();

echo "Total records: " . $tagihan->count() . "\n\n";

$grouped = [];
foreach ($tagihan as $t) {
    $key = $t->nomor_kontainer;
    if (!isset($grouped[$key])) {
        $grouped[$key] = [];
    }
    $grouped[$key][] = $t;
}

foreach ($grouped as $container => $items) {
    echo "Container: {$container}\n";
    $periodeCount = [];
    foreach ($items as $item) {
        $p = $item->periode;
        if (!isset($periodeCount[$p])) {
            $periodeCount[$p] = 0;
        }
        $periodeCount[$p]++;
    }
    
    foreach ($periodeCount as $periode => $count) {
        if ($count > 1) {
            echo "  Periode {$periode}: {$count} records DUPLIKAT\n";
        }
    }
    echo "\n";
}

echo "\n=== Cek Semua Duplikat ===\n\n";

$duplicates = DB::select("
    SELECT nomor_kontainer, periode, vendor, COUNT(*) as count,
           GROUP_CONCAT(id ORDER BY id) as ids,
           GROUP_CONCAT(masa ORDER BY id SEPARATOR ' | ') as masas
    FROM daftar_tagihan_kontainer_sewa
    GROUP BY nomor_kontainer, periode
    HAVING COUNT(*) > 1
    ORDER BY nomor_kontainer, periode
    LIMIT 30
");

foreach ($duplicates as $dup) {
    echo "{$dup->nomor_kontainer} (Vendor: {$dup->vendor}) - Periode {$dup->periode}: {$dup->count} records\n";
    echo "  IDs: {$dup->ids}\n";
    echo "  Masa: {$dup->masas}\n";
    echo "\n";
}
