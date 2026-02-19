<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIX ALL MISSING CARGO MANIFESTS ===\n";
echo "Finding all CARGO NaikKapal that are OB but have no matching manifest...\n\n";

$missing = App\Models\NaikKapal::where('sudah_ob', true)
    ->where(function($q) {
        $q->where('tipe_kontainer', 'CARGO')
          ->orWhere('nomor_kontainer', 'like', '%CARGO%');
    })
    ->orderBy('updated_at', 'desc')
    ->get(['id','nomor_kontainer','tipe_kontainer','jenis_barang','nama_kapal','no_voyage','updated_at','no_seal','size_kontainer','total_tonase','total_volume','asal_kontainer','ke','prospek_id']);

$created = 0;
$skipped = 0;

foreach ($missing as $nk) {
    // Check if EXACT manifest already exists for this item
    $exists = App\Models\Manifest::where('nama_kapal', $nk->nama_kapal)
        ->where('no_voyage', $nk->no_voyage)
        ->where('tipe_kontainer', 'CARGO')
        ->where('nama_barang', $nk->jenis_barang)
        ->exists();

    if ($exists) {
        echo "✓ ID {$nk->id} ({$nk->jenis_barang}) - already has manifest, SKIP\n";
        $skipped++;
        continue;
    }

    // Create manifest
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
    $manifest->tanggal_berangkat = now();

    if ($nk->prospek_id) {
        $prospek = App\Models\Prospek::find($nk->prospek_id);
        if ($prospek) {
            $manifest->prospek_id = $nk->prospek_id;
            $manifest->pengirim = $prospek->pt_pengirim;
            $manifest->penerima = $prospek->tujuan_pengiriman;
        }
    }

    $lastManifest = App\Models\Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
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

    echo "✅ ID {$nk->id} ({$nk->jenis_barang}) - Created manifest {$manifest->nomor_bl}\n";
    $created++;
}

echo "\n=== SELESAI ===\n";
echo "Created: $created manifest baru\n";
echo "Skipped: $skipped (already had manifest)\n";
