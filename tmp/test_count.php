<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$kapalNama = 'KM. SEKAR PERMATA';
$voyage = 'SP04PJ26';

echo "Querying for Kapal: $kapalNama, Voyage: $voyage\n\n";

$bls = DB::table('bls')
    ->select('nama_barang', 'size_kontainer', 'nomor_kontainer', 'tipe_kontainer', 'sudah_ob', 'sudah_tl')
    ->where('nama_kapal', 'like', "%{$kapalNama}%")
    ->where('no_voyage', 'like', "%{$voyage}%")
    ->get();

echo "BLS (Bongkar) items count: " . $bls->count() . "\n";
foreach($bls as $b) {
    echo "- " . ($b->nomor_kontainer ?? 'N/A') . " | " . ($b->nama_barang ?? '') . " | sudah_ob: " . json_encode($b->sudah_ob) . " | sudah_tl: " . json_encode($b->sudah_tl) . "\n";
}

$naikKapals = DB::table('naik_kapal')
    ->select('jenis_barang as nama_barang', 'size_kontainer', 'nomor_kontainer', 'tipe_kontainer', 'sudah_ob', 'is_tl as sudah_tl')
    ->where('nama_kapal', 'like', "%{$kapalNama}%")
    ->where('no_voyage', 'like', "%{$voyage}%")
    ->get();

echo "\nNaikKapal (Muat) items count: " . $naikKapals->count() . "\n";
foreach($naikKapals as $n) {
    echo "- " . ($n->nomor_kontainer ?? 'N/A') . " | " . ($n->nama_barang ?? '') . " | sudah_ob: " . json_encode($n->sudah_ob) . " | sudah_tl: " . json_encode($n->sudah_tl) . "\n";
}
