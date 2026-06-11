<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$voyage = 'SA09BJ26';

echo "=== NAIK KAPAL ===\n";
$naikKapal = DB::table('naik_kapal')->where('no_voyage', $voyage)->get();
foreach ($naikKapal as $nk) {
    echo "ID: {$nk->id}, Nama Kapal: {$nk->nama_kapal}, Tanggal Muat: {$nk->tanggal_muat}, Created At: {$nk->created_at}\n";
}

echo "\n=== BLS ===\n";
$bls = DB::table('bls')->where('no_voyage', $voyage)->get();
foreach ($bls as $bl) {
    echo "ID: {$bl->id}, Nama Kapal: {$bl->nama_kapal}, Tanggal Berangkat: {$bl->tanggal_berangkat}, Created At: {$bl->created_at}\n";
}
