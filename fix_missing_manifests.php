<?php
// Script untuk membuat manifest yang hilang dari NaikKapal yang sudah OB
// Jalankan: php artisan tinker < fix_missing_manifests.php

// NaikKapal yang sudah OB tapi belum ada manifest
$naikKapalIds = [4944, 4945, 4946];

foreach ($naikKapalIds as $nkId) {
    $nk = App\Models\NaikKapal::with('prospek')->find($nkId);
    if (!$nk) {
        echo "NaikKapal $nkId not found\n";
        continue;
    }

    echo "Processing NaikKapal $nkId: {$nk->nomor_kontainer} | {$nk->tipe_kontainer} | {$nk->nama_kapal} | {$nk->no_voyage}\n";

    // Buat manifest
    $manifest = new App\Models\Manifest();
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
    $manifest->tanggal_berangkat = $nk->tanggal_ob ?? now();

    if ($nk->prospek_id && $nk->prospek) {
        $manifest->prospek_id = $nk->prospek_id;
        $manifest->pengirim = $nk->prospek->pt_pengirim;
        $manifest->penerima = $nk->prospek->tujuan_pengiriman;
    }

    // Generate nomor BL
    $lastManifest = App\Models\Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
    if ($lastManifest && $lastManifest->nomor_bl) {
        preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
        $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
        $manifest->nomor_bl = 'MNF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $manifest->nomor_bl = 'MNF-000001';
    }

    $manifest->created_by = 1; // Admin user
    $manifest->updated_by = 1;
    $manifest->save();

    echo "Created manifest: {$manifest->nomor_bl} (id: {$manifest->id})\n";
}

echo "\nDone!\n";
