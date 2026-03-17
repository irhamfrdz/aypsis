<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$kapalNama = 'KM. SUMBER ABADI 178';
$voyage = 'SA05BJ26';

$cleanKapalNama = str_replace('.', '', $kapalNama);

echo "Querying for Original: '$kapalNama' | Clean: '$cleanKapalNama'\n";

$bls = DB::table('bls')
    ->where('nama_kapal', 'like', "%{$cleanKapalNama}%")
    ->where('no_voyage', $voyage)
    ->get();

echo "BLS row count: " . $bls->count() . "\n";
foreach($bls as $b) {
    echo "- BLS: " . ($b->nomor_kontainer ?? 'N/A') . " | Size: " . ($b->size_kontainer ?? '') . " | Nama: " . ($b->nama_barang ?? '') . "\n";
}

$naikKapals = DB::table('naik_kapal')
    ->where('nama_kapal', 'like', "%{$cleanKapalNama}%")
    ->where('no_voyage', $voyage)
    ->get();

echo "\nNaikKapal row count: " . $naikKapals->count() . "\n";
foreach($naikKapals as $n) {
    echo "- NK: " . ($n->nomor_kontainer ?? 'N/A') . " | Size: " . ($n->size_kontainer ?? '') . " | Jenis: " . ($n->jenis_barang ?? '') . "\n";
}
