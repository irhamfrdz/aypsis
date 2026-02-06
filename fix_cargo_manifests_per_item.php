<?php

/**
 * Script untuk membuat manifest terpisah untuk SETIAP item CARGO
 * Karena semua CARGO menggunakan nomor_kontainer "CARGO", 
 * maka perlu dibuat manifest terpisah berdasarkan naik_kapal_id
 * 
 * Cara menjalankan:
 * php fix_cargo_manifests_per_item.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

echo "===== FIX CARGO MANIFESTS - SETIAP ITEM TERPISAH =====\n";
echo "Membuat manifest terpisah untuk setiap kontainer CARGO...\n\n";

try {
    DB::beginTransaction();
    
    // Cari semua kontainer CARGO yang sudah OB
    $cargoItems = NaikKapal::where('tipe_kontainer', 'CARGO')
        ->where('sudah_ob', true)
        ->whereNotNull('nomor_kontainer')
        ->whereNotNull('nama_kapal')
        ->whereNotNull('no_voyage')
        ->with('prospek.tandaTerima')
        ->get();
    
    echo "Ditemukan " . $cargoItems->count() . " item CARGO yang sudah OB\n\n";
    
    if ($cargoItems->count() === 0) {
        echo "⚠️ Tidak ada kontainer CARGO yang sudah OB\n";
        DB::rollBack();
        return;
    }
    
    $created = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($cargoItems as $naikKapal) {
        echo "═══════════════════════════════════════════\n";
        echo "ID Naik Kapal: {$naikKapal->id}\n";
        echo "Nomor Kontainer: {$naikKapal->nomor_kontainer}\n";
        echo "Jenis Barang: {$naikKapal->jenis_barang}\n";
        echo "Kapal: {$naikKapal->nama_kapal}\n";
        echo "Voyage: {$naikKapal->no_voyage}\n";
        
        // Buat nomor kontainer unik untuk CARGO berdasarkan ID
        $cargoNomorKontainer = "CARGO-{$naikKapal->id}";
        echo "Nomor Kontainer Unik: {$cargoNomorKontainer}\n";
        
        // Cek apakah sudah ada manifest dengan kombinasi nomor kontainer unik + voyage + kapal + nama barang
        $existingManifest = Manifest::where('nomor_kontainer', $cargoNomorKontainer)
            ->where('no_voyage', $naikKapal->no_voyage)
            ->where('nama_kapal', $naikKapal->nama_kapal)
            ->first();
        
        if ($existingManifest) {
            echo "✓ Manifest sudah ada (ID: {$existingManifest->id}, Nomor: {$existingManifest->nomor_bl})\n";
            $skipped++;
            continue;
        }
        
        try {
            echo "→ Membuat manifest baru...\n";
            
            // Buat manifest baru
            $manifest = new Manifest();
            
            // Buat nomor kontainer unik untuk CARGO
            $manifest->nomor_kontainer = $cargoNomorKontainer;
            
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
            
            $manifest->created_by = 1;
            $manifest->updated_by = 1;
            
            $manifest->save();
            
            echo "✅ Manifest berhasil dibuat!\n";
            echo "  ID: {$manifest->id}\n";
            echo "  Nomor: {$manifest->nomor_bl}\n";
            echo "  Nomor Kontainer: {$manifest->nomor_kontainer}\n";
            $created++;
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            $errors++;
        }
        
        echo "\n";
    }
    
    DB::commit();
    
    echo "═══════════════════════════════════════════\n";
    echo "===== RINGKASAN =====\n";
    echo "Total item CARGO: " . $cargoItems->count() . "\n";
    echo "Manifest dibuat: $created\n";
    echo "Sudah ada (dilewati): $skipped\n";
    echo "Error: $errors\n\n";
    
    if ($created > 0) {
        echo "✅ Berhasil membuat $created manifest baru untuk item CARGO!\n";
        echo "\nSetiap item CARGO sekarang memiliki manifest terpisah dengan nomor kontainer unik.\n";
    } elseif ($skipped > 0) {
        echo "ℹ️ Semua item CARGO sudah memiliki manifest.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
