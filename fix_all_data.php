<?php
require 'vendor/autoload.php'; 
$app = require_once 'bootstrap/app.php'; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

echo "Memulai pengecekan dan perbaikan data...\n";

// Ambil semua Tanda Terima Tanpa Surat Jalan beserta dimensi itemsnya
$ttsjs = \App\Models\TandaTerimaTanpaSuratJalan::with('dimensiItems')->get();
$updatedCount = 0;
$manifestCount = 0;

foreach($ttsjs as $tt) {
    if($tt->dimensiItems->count() > 0) {
        $namaBarangArray = $tt->dimensiItems->pluck('nama_barang')->filter()->map(function($item) {
            return trim($item);
        })->unique()->toArray();
        
        if(!empty($namaBarangArray)) {
            // Truncate to 255 characters to avoid SQL error (Data too long for column 'jenis_barang')
            $combinedNames = Str::limit(implode(', ', $namaBarangArray), 250, '...');
            
            $needsUpdate = false;
            
            // Cek apakah di Tanda Terima perlu diupdate
            if($tt->jenis_barang !== $combinedNames || $tt->nama_barang !== $combinedNames) {
                $tt->update([
                    'nama_barang' => $combinedNames,
                    'jenis_barang' => $combinedNames
                ]);
                $needsUpdate = true;
            }
            
            $noTandaTerima = $tt->no_tanda_terima ?: $tt->nomor_tanda_terima;
            
            if($noTandaTerima) {
                // Cari Prospek yang terkait
                $prospeks = \App\Models\Prospek::where('no_surat_jalan', $noTandaTerima)
                    ->orWhere('keterangan', 'like', "%Tanda Terima Tanpa Surat Jalan: {$noTandaTerima}%")
                    ->get();
                    
                foreach($prospeks as $prospek) {
                    if($prospek->barang !== $combinedNames) {
                        $prospek->update(['barang' => $combinedNames]);
                        echo "Prospek ID {$prospek->id} (No SJ: {$noTandaTerima}) diupdate -> {$combinedNames}\n";
                        $needsUpdate = true;
                        
                        // Update Naik Kapal yang terkait dengan prospek ini
                        $naikKapals = \App\Models\NaikKapal::where('prospek_id', $prospek->id)->get();
                        foreach($naikKapals as $nk) {
                            if($nk->jenis_barang !== $combinedNames) {
                                $nk->update(['jenis_barang' => $combinedNames]);
                                echo "  - NaikKapal ID {$nk->id} diupdate -> {$combinedNames}\n";
                            }
                        }
                    }
                    
                    // Update Manifest based on prospek_id
                    $manifests = \App\Models\Manifest::where('prospek_id', $prospek->id)->get();
                    foreach($manifests as $manifest) {
                        if($manifest->nama_barang !== $combinedNames) {
                            $manifest->update(['nama_barang' => $combinedNames]);
                            echo "  - Manifest ID {$manifest->id} diupdate -> {$combinedNames}\n";
                            $manifestCount++;
                            $needsUpdate = true;
                        }
                    }
                }
                
                // Update Manifest based on nomor_tanda_terima (just in case they aren't linked by prospek_id)
                $manifestsByNo = \App\Models\Manifest::where('nomor_tanda_terima', $noTandaTerima)->get();
                foreach($manifestsByNo as $manifest) {
                    if($manifest->nama_barang !== $combinedNames) {
                        $manifest->update(['nama_barang' => $combinedNames]);
                        echo "  - Manifest ID {$manifest->id} (By No TT) diupdate -> {$combinedNames}\n";
                        $manifestCount++;
                        $needsUpdate = true;
                    }
                }
            }
            
            if($needsUpdate) {
                $updatedCount++;
            }
        }
    }
}

echo "Selesai! Total {$updatedCount} rangkaian data (termasuk {$manifestCount} Manifest) telah diperbaiki.\n";
