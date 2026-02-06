<?php

/**
 * Script untuk membuat manifest yang hilang untuk kontainer CARGO
 * yang sudah OB tapi belum ada record manifestnya
 * 
 * Cara menjalankan:
 * php fix_cargo_manifests.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

echo "===== FIX CARGO MANIFESTS =====\n";
echo "Mulai memeriksa kontainer CARGO yang sudah OB...\n\n";

try {
    DB::beginTransaction();
    
    // Cari semua naik_kapal yang sudah OB
    $naikKapals = NaikKapal::where('sudah_ob', true)
        ->whereNotNull('nomor_kontainer')
        ->whereNotNull('nama_kapal')
        ->whereNotNull('no_voyage')
        ->with('prospek.tandaTerima')
        ->get();
    
    echo "Ditemukan " . $naikKapals->count() . " kontainer yang sudah OB\n\n";
    
    $created = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($naikKapals as $naikKapal) {
        echo "Memeriksa: {$naikKapal->nomor_kontainer} ({$naikKapal->tipe_kontainer}) - {$naikKapal->nama_kapal} / {$naikKapal->no_voyage}\n";
        
        // Cek apakah sudah ada manifest untuk kontainer ini
        $existingManifest = Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
            ->where('no_voyage', $naikKapal->no_voyage)
            ->where('nama_kapal', $naikKapal->nama_kapal)
            ->first();
        
        if ($existingManifest) {
            echo "  ✓ Manifest sudah ada (ID: {$existingManifest->id}, Nomor: {$existingManifest->nomor_bl})\n";
            $skipped++;
            continue;
        }
        
        try {
            echo "  → Membuat manifest baru...\n";
            
            // Buat manifest baru
            $manifest = new Manifest();
            $manifest->nomor_kontainer = $naikKapal->nomor_kontainer;
            $manifest->no_seal = $naikKapal->no_seal;
            $manifest->tipe_kontainer = $naikKapal->tipe_kontainer;
            $manifest->size_kontainer = $naikKapal->size_kontainer;
            $manifest->nama_kapal = $naikKapal->nama_kapal;
            $manifest->no_voyage = $naikKapal->no_voyage;
            $manifest->nama_barang = $naikKapal->jenis_barang;
            $manifest->volume = $naikKapal->total_volume;
            $manifest->tonnage = $naikKapal->total_tonase;
            $manifest->pelabuhan_muat = $naikKapal->asal_kontainer;
            $manifest->pelabuhan_bongkar = $naikKapal->ke;
            $manifest->tanggal_berangkat = $naikKapal->tanggal_ob ?? now();
            
            // Data pengirim/penerima dari prospek jika ada
            if ($naikKapal->prospek_id && $naikKapal->prospek) {
                $manifest->prospek_id = $naikKapal->prospek_id;
                $manifest->pengirim = $naikKapal->prospek->pt_pengirim;
                
                // Cek apakah ada penerima di prospek
                $penerima = null;
                if ($naikKapal->prospek->tandaTerima) {
                    $penerima = $naikKapal->prospek->tandaTerima->penerima;
                    $manifest->alamat_penerima = $naikKapal->prospek->tandaTerima->alamat_penerima;
                }
                $manifest->penerima = $penerima ?? $naikKapal->prospek->tujuan_pengiriman;
            }
            
            // Generate nomor manifest
            $lastManifest = Manifest::whereNotNull('nomor_bl')
                ->orderBy('id', 'desc')
                ->first();
            
            if ($lastManifest && $lastManifest->nomor_bl) {
                preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
                $manifest->nomor_bl = 'MNF-' . $nextNumber;
            } else {
                $manifest->nomor_bl = 'MNF-000001';
            }
            
            // Set user yang membuat (gunakan admin/system user ID 1)
            $manifest->created_by = 1;
            $manifest->updated_by = 1;
            
            $manifest->save();
            
            echo "  ✓ Manifest berhasil dibuat (ID: {$manifest->id}, Nomor: {$manifest->nomor_bl})\n";
            $created++;
            
        } catch (\Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
            $errors++;
        }
        
        echo "\n";
    }
    
    DB::commit();
    
    echo "\n===== SELESAI =====\n";
    echo "Total diperiksa: " . $naikKapals->count() . "\n";
    echo "Manifest dibuat: $created\n";
    echo "Sudah ada (dilewati): $skipped\n";
    echo "Error: $errors\n";
    
    if ($created > 0) {
        echo "\n✓ Berhasil membuat $created manifest baru!\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
