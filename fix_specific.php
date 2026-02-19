<?php

use App\Models\NaikKapal;
use App\Models\Manifest;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = 4945; // The remaining missing one
$nk = NaikKapal::with('prospek')->find($id);

if ($nk) {
    echo "Creating missing duplicate manifest for NaikKapal ID $id (PLATE)...\n";
    $manifest = new \App\Models\Manifest();
    $manifest->nomor_kontainer = $nk->nomor_kontainer;
    $manifest->no_seal = $nk->no_seal;
    $manifest->tipe_kontainer = $nk->tipe_kontainer;
    $manifest->size_kontainer = $nk->size_kontainer;
    $manifest->nama_kapal = $nk->nama_kapal;
    $manifest->no_voyage = $nk->no_voyage;
    $manifest->nama_barang = $nk->jenis_barang;
    $manifest->volume = $nk->total_volume;
    $manifest->tonnage = $nk->total_tonase;
    $manifest->pelabuhan_muat = $nk->asal_kontainer;
    $manifest->pelabuhan_bongkar = $nk->ke;
    $manifest->tanggal_berangkat = now();

    if ($nk->prospek_id && $nk->prospek) {
        $manifest->prospek_id = $nk->prospek_id;
        $manifest->pengirim = $nk->prospek->pt_pengirim;
        $manifest->penerima = $nk->prospek->tujuan_pengiriman;
    }

    $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
    if ($lastManifest && $lastManifest->nomor_bl) {
        preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
        $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
        $manifest->nomor_bl = 'MNF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $manifest->nomor_bl = 'MNF-000001';
    }

    $manifest->created_by = 1;
    $manifest->updated_by = 1;
    $manifest->save();

    echo ">> SUCCESS: Created manifest ID {$manifest->id} with BL {$manifest->nomor_bl}\n";
} else {
    echo "NaikKapal $id not found.\n";
}
