<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$voyage = 'SR01JB26';

// Get all naik_kapal that have sudah_ob = 1
$naikKapalOb = DB::table('naik_kapal')
    ->where('no_voyage', $voyage)
    ->where('sudah_ob', 1)
    ->get();

echo "Total naik_kapal with sudah_ob=1: " . $naikKapalOb->count() . "\n";

// Get all BLS for this voyage
$bls = DB::table('bls')
    ->where('no_voyage', $voyage)
    ->get();

echo "Total BLS: " . $bls->count() . "\n\n";

// Find missing
$missingCount = 0;
$missing = [];

foreach ($naikKapalOb as $nk) {
    $found = DB::table('bls')
        ->where('no_voyage', $voyage)
        ->where('nama_kapal', $nk->nama_kapal)
        ->where('nomor_kontainer', $nk->nomor_kontainer)
        ->exists();
    
    if (!$found) {
        $missingCount++;
        $missing[] = [
            'id' => $nk->id,
            'nomor_kontainer' => $nk->nomor_kontainer,
            'jenis_barang' => $nk->jenis_barang,
            'is_tl' => $nk->is_tl,
            'tipe_kontainer' => $nk->tipe_kontainer
        ];
    }
}

echo "Missing BLS records: " . $missingCount . "\n\n";

if ($missingCount > 0) {
    echo "Missing containers:\n";
    foreach ($missing as $m) {
        $tlFlag = $m['is_tl'] ? ' [TL]' : '';
        echo "- ID: {$m['id']}, Kontainer: {$m['nomor_kontainer']}, Barang: {$m['jenis_barang']}, Tipe: {$m['tipe_kontainer']}{$tlFlag}\n";
    }
}
