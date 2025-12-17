<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Sample Data Kolom Masa ===\n\n";

$samples = DB::table('daftar_tagihan_kontainer_sewa')
    ->whereNotNull('masa')
    ->limit(20)
    ->get(['nomor_kontainer', 'periode', 'masa', 'tarif', 'tanggal_awal', 'tanggal_akhir']);

foreach ($samples as $s) {
    echo "Kontainer: {$s->nomor_kontainer}\n";
    echo "  Periode: {$s->periode}\n";
    echo "  Masa: '{$s->masa}' (type: " . gettype($s->masa) . ")\n";
    echo "  Tarif: {$s->tarif}\n";
    echo "  Tanggal: {$s->tanggal_awal} s/d {$s->tanggal_akhir}\n";
    echo "---\n";
}

echo "\n=== Sample dari Screenshot RXTU4540180 ===\n\n";
$rxtu = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'RXTU4540180')
    ->orderBy('periode')
    ->limit(10)
    ->get(['periode', 'masa', 'tarif', 'size']);

foreach ($rxtu as $r) {
    echo "Periode {$r->periode}: Masa='{$r->masa}', Tarif={$r->tarif}, Size={$r->size}\n";
}
