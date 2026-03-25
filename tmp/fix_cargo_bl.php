<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bl;
use App\Models\TandaTerimaTanpaSuratJalan;
use Illuminate\Support\Facades\DB;

$bls = Bl::where(function($q) {
    $q->where('nomor_kontainer', 'like', '%cargo%')
      ->orWhere('nomor_kontainer', 'like', '%cargp%');
})->get();

$updatedFieldsCount = 0;
$updatedBlsCount = 0;

foreach ($bls as $bl) {
    $needsUpdate = false;
    
    // Check if BL derives from a prospek
    if ($bl->prospek) {
        $prospek = $bl->prospek;
        
        // Find TTTSJ based on Prospek
        $tttsj = TandaTerimaTanpaSuratJalan::where('no_tanda_terima', $prospek->no_surat_jalan)->first();
        
        if ($tttsj) {
            // Check & Update pengirim
            if (!$bl->pengirim || $bl->pengirim === 'Tidak diketahui' || $bl->pengirim === '') {
                $bl->pengirim = $tttsj->pengirim;
                $needsUpdate = true;
            }
            
            // Check & Update penerima
            if (!$bl->penerima || $bl->penerima === 'Tidak diketahui' || $bl->penerima === '') {
                $bl->penerima = $tttsj->penerima;
                $needsUpdate = true;
            }
            
            // Check & Update tonnage
            if (!$bl->tonnage || $bl->tonnage == 0) {
                $bl->tonnage = $tttsj->total_tonase;
                $needsUpdate = true;
            }
            
            // Check & Update volume
            if (!$bl->volume || $bl->volume == 0) {
                $bl->volume = $tttsj->total_volume;
                $needsUpdate = true;
            }

            // Check & update term
            if (!$bl->term) {
                $bl->term = $tttsj->term;
                $needsUpdate = true;
            }

            // check and update alamat
            if (!$bl->alamat_pengiriman || $bl->alamat_pengiriman == 'Tidak diketahui') {
                $bl->alamat_pengiriman = $tttsj->alamat_penerima ?: $tttsj->tujuan_pengiriman;
                $needsUpdate = true;
            }

            // nama_barang
            if (!$bl->nama_barang || $bl->nama_barang == 'Tidak diketahui') {
                $nama_barang = $tttsj->jenis_barang;
                if (!$nama_barang && $tttsj->nama_barang) {
                    $nama_barang = is_array($tttsj->nama_barang) ? implode(', ', $tttsj->nama_barang) : $tttsj->nama_barang;
                }
                if ($nama_barang) {
                    $bl->nama_barang = $nama_barang;
                    $needsUpdate = true;
                }
            }
            
            if ($needsUpdate) {
                $bl->save();
                $updatedBlsCount++;
                echo "Updated BL ID: {$bl->id} with TTTSJ ID: {$tttsj->id} (Tonnage: {$bl->tonnage}, Volume: {$bl->volume})\n";
            }
        }
    }
}

echo "Done. Updated {$updatedBlsCount} BL records.\n";
