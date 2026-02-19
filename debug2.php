<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Recent CARGO NaikKapal OB Records ===\n";
$recent = App\Models\NaikKapal::where('sudah_ob', true)
    ->where('tipe_kontainer', 'CARGO')
    ->orderBy('updated_at', 'desc')
    ->limit(10)
    ->get(['id','nomor_kontainer','tipe_kontainer','jenis_barang','nama_kapal','no_voyage','tanggal_ob','sudah_ob','updated_at']);

foreach ($recent as $nk) {
    echo "\nNaikKapal ID: {$nk->id}\n";
    echo "  Barang: {$nk->jenis_barang}\n";
    echo "  Kapal: {$nk->nama_kapal} | Voyage: {$nk->no_voyage}\n";
    echo "  Updated At: {$nk->updated_at}\n";
    
    $manifest = App\Models\Manifest::where('nama_kapal', $nk->nama_kapal)
        ->where('no_voyage', $nk->no_voyage)
        ->where('nama_barang', $nk->jenis_barang)
        ->where('tipe_kontainer', 'CARGO')
        ->first();
    
    if ($manifest) {
        echo "  -> Manifest EXISTS: ID={$manifest->id}, BL={$manifest->nomor_bl}\n";
    } else {
        echo "  -> Manifest MISSING!\n";
        
        // Check if ANY manifest exists for this voyage+kapal
        $anyManifest = App\Models\Manifest::where('nama_kapal', $nk->nama_kapal)
            ->where('no_voyage', $nk->no_voyage)
            ->where('tipe_kontainer', 'CARGO')
            ->get(['id','nomor_bl','nama_barang','created_at']);
        
        if ($anyManifest->count() > 0) {
            echo "  -> Other CARGO manifests for same voyage:\n";
            foreach ($anyManifest as $m) {
                echo "     - ID={$m->id}, BL={$m->nomor_bl}, Barang={$m->nama_barang}, Created={$m->created_at}\n";
            }
        } else {
            echo "  -> NO CARGO manifests at all for voyage {$nk->no_voyage}\n";
        }
    }
}
