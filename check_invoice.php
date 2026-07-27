<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$biaya = \DB::table('biaya_kapals')->where('nomor_invoice', 'BKP-02-26-000044')->first();
if ($biaya) {
    echo "Old no_bl: " . $biaya->no_bl . "\n";
    echo "Old nama_kapal: " . $biaya->nama_kapal . "\n";
    echo "Old no_voyage: " . $biaya->no_voyage . "\n";
    
    $dokumens = \DB::table('biaya_kapal_dokumens')->where('biaya_kapal_id', $biaya->id)->get();
    foreach ($dokumens as $dok) {
        echo "Dokumen: {$dok->kapal} - {$dok->voyage} => BLs: {$dok->nomor_bl}\n";
    }
}
