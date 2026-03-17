<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$voyage = 'SA05BJ26';

echo "--- Any row with Voyage '$voyage' in 'bls' ---\n";
$itemsBls = DB::table('bls')
    ->where('no_voyage', $voyage)
    ->select('nama_kapal', 'nomor_kontainer')
    ->get();

foreach($itemsBls as $i) {
    echo "- Kapal: '$i->nama_kapal'\n";
}

echo "\n--- Any row with Voyage '$voyage' in 'naik_kapal' ---\n";
$itemsNk = DB::table('naik_kapal')
    ->where('no_voyage', $voyage)
    ->select('nama_kapal', 'nomor_kontainer')
    ->get();

foreach($itemsNk as $n) {
    echo "- Kapal: '$n->nama_kapal'\n";
}
