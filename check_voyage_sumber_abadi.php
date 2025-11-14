<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Cek data BL untuk kapal SUMBER ABADI\n";
echo "========================================\n\n";

// Cek semua data BL dengan nama kapal mengandung SUMBER ABADI
$data = DB::table('bls')
    ->select('id', 'nama_kapal', 'no_voyage', 'nomor_kontainer')
    ->where('nama_kapal', 'like', '%SUMBER ABADI%')
    ->get();

echo "Total data: " . $data->count() . "\n\n";

if ($data->count() > 0) {
    foreach ($data as $row) {
        echo "ID: {$row->id}\n";
        echo "Nama Kapal: {$row->nama_kapal}\n";
        echo "No Voyage: {$row->no_voyage}\n";
        echo "Nomor Kontainer: {$row->nomor_kontainer}\n";
        echo "----------------------------------------\n";
    }
    
    echo "\n\nVoyage unik untuk kapal SUMBER ABADI:\n";
    echo "========================================\n";
    
    $voyages = DB::table('bls')
        ->where('nama_kapal', 'like', '%SUMBER ABADI%')
        ->whereNotNull('no_voyage')
        ->distinct()
        ->pluck('no_voyage')
        ->toArray();
    
    foreach ($voyages as $voyage) {
        echo "- {$voyage}\n";
    }
} else {
    echo "Tidak ada data BL untuk kapal SUMBER ABADI\n";
}

echo "\n\nCek master kapal:\n";
echo "========================================\n";

$masterKapals = DB::table('master_kapals')
    ->select('id', 'nama_kapal', 'nickname')
    ->where('nama_kapal', 'like', '%SUMBER ABADI%')
    ->get();

foreach ($masterKapals as $kapal) {
    echo "ID: {$kapal->id}\n";
    echo "Nama Kapal: {$kapal->nama_kapal}\n";
    echo "Nickname: " . ($kapal->nickname ?: '-') . "\n";
    echo "----------------------------------------\n";
}
