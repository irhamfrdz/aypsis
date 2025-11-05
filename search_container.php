<?php

require_once 'bootstrap/app.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SEARCHING FOR CONTAINER VARIATIONS ===\n";

// Search for similar container numbers
$containers = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('no_kontainer', 'like', '%RXTU548%')
    ->select('no_kontainer', 'tanggal_mulai', 'tanggal_akhir', 'periode')
    ->orderBy('no_kontainer')
    ->orderBy('periode')
    ->get();

echo "Found containers with RXTU548: " . $containers->count() . "\n\n";

$grouped = [];
foreach($containers as $c) {
    $grouped[$c->no_kontainer][] = $c;
}

foreach($grouped as $containerNo => $records) {
    echo "Container: $containerNo\n";
    foreach($records as $record) {
        echo "  Periode: {$record->periode}, Start: {$record->tanggal_mulai}, End: {$record->tanggal_akhir}\n";
    }
    echo "\n";
}

// Also search for any container that has exactly 7 periods
echo "=== CONTAINERS WITH EXACTLY 7 PERIODS ===\n";
$containers7 = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('no_kontainer', DB::raw('MAX(periode) as max_periode'))
    ->groupBy('no_kontainer')
    ->having('max_periode', '=', 7)
    ->get();

foreach($containers7 as $c) {
    echo "Container: {$c->no_kontainer} - Max Periode: {$c->max_periode}\n";
}

echo "\n=== COMPLETE ===\n";