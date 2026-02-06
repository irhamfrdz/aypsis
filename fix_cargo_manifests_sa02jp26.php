<?php

/**
 * Script untuk membuat manifest khusus untuk voyage SA02JP26
 * Memeriksa semua kontainer CARGO yang sudah OB untuk voyage ini
 * 
 * Cara menjalankan:
 * php fix_cargo_manifests_sa02jp26.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

echo "===== FIX CARGO MANIFESTS - VOYAGE SA02JP26 =====\n";
echo "Memeriksa kontainer untuk voyage SA02JP26...\n\n";

try {
    DB::beginTransaction();
    
    $targetVoyage = 'SA02JP26';
    
    // Cari semua naik_kapal untuk voyage ini (tidak peduli sudah OB atau belum)
    $naikKapals = NaikKapal::where('no_voyage', $targetVoyage)
        ->whereNotNull('nomor_kontainer')
        ->whereNotNull('nama_kapal')
        ->with('prospek.tandaTerima')
        ->get();
    
    echo "Ditemukan " . $naikKapals->count() . " kontainer untuk voyage $targetVoyage\n\n";
    
    if ($naikKapals->count() === 0) {
        echo "⚠️ TIDAK ADA DATA di tabel naik_kapal untuk voyage $targetVoyage\n";
        echo "Kemungkinan:\n";
        echo "1. Voyage salah atau tidak ada di tabel naik_kapal\n";
        echo "2. Data kontainer belum diinput ke tabel naik_kapal\n\n";
        
        echo "Memeriksa tabel BLS untuk voyage ini...\n";
        $blRecords = \App\Models\Bl::where('no_voyage', $targetVoyage)->get();
        echo "Ditemukan " . $blRecords->count() . " record di BLS\n\n";
        
        if ($blRecords->count() > 0) {
            echo "Data dari BLS:\n";
            foreach ($blRecords as $bl) {
                echo "  - {$bl->nomor_kontainer} ({$bl->tipe_kontainer}) - OB: " . ($bl->sudah_ob ? 'Ya' : 'Belum') . "\n";
            }
        }
        
        DB::rollBack();
        return;
    }
    
    $created = 0;
    $skipped = 0;
    $errors = 0;
    $notOB = 0;
    
    foreach ($naikKapals as $naikKapal) {
        echo "═══════════════════════════════════════════\n";
        echo "Kontainer: {$naikKapal->nomor_kontainer}\n";
        echo "Tipe: {$naikKapal->tipe_kontainer}\n";
        echo "Kapal: {$naikKapal->nama_kapal}\n";
        echo "Voyage: {$naikKapal->no_voyage}\n";
        echo "Status OB: " . ($naikKapal->sudah_ob ? 'Sudah' : 'BELUM') . "\n";
        echo "Tanggal OB: " . ($naikKapal->tanggal_ob ?? '-') . "\n";
        echo "Jenis Barang: " . ($naikKapal->jenis_barang ?? '-') . "\n";
        
        // Cek status OB
        if (!$naikKapal->sudah_ob) {
            echo "❌ SKIP: Kontainer belum ditandai OB\n";
            $notOB++;
            continue;
        }
        
        // Cek apakah sudah ada manifest untuk kontainer ini
        $existingManifest = Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
            ->where('no_voyage', $naikKapal->no_voyage)
            ->where('nama_kapal', $naikKapal->nama_kapal)
            ->first();
        
        if ($existingManifest) {
            echo "✓ Manifest sudah ada\n";
            echo "  ID: {$existingManifest->id}\n";
            echo "  Nomor: {$existingManifest->nomor_bl}\n";
            echo "  Tipe: {$existingManifest->tipe_kontainer}\n";
            $skipped++;
            continue;
        }
        
        try {
            echo "→ Membuat manifest baru...\n";
            
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
                
                $penerima = null;
                if ($naikKapal->prospek->tandaTerima) {
                    $penerima = $naikKapal->prospek->tandaTerima->penerima;
                    $manifest->alamat_penerima = $naikKapal->prospek->tandaTerima->alamat_penerima;
                }
                $manifest->penerima = $penerima ?? $naikKapal->prospek->tujuan_pengiriman;
                
                echo "  Prospek ID: {$naikKapal->prospek_id}\n";
                echo "  Pengirim: " . ($manifest->pengirim ?? '-') . "\n";
                echo "  Penerima: " . ($manifest->penerima ?? '-') . "\n";
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
            $created++;
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
            $errors++;
        }
    }
    
    DB::commit();
    
    echo "\n═══════════════════════════════════════════\n";
    echo "===== RINGKASAN =====\n";
    echo "Voyage: $targetVoyage\n";
    echo "Total kontainer: " . $naikKapals->count() . "\n";
    echo "Manifest dibuat: $created\n";
    echo "Sudah ada (dilewati): $skipped\n";
    echo "Belum OB (dilewati): $notOB\n";
    echo "Error: $errors\n\n";
    
    if ($created > 0) {
        echo "✅ Berhasil membuat $created manifest baru untuk voyage $targetVoyage!\n";
    } elseif ($notOB > 0) {
        echo "⚠️ Ada $notOB kontainer yang belum ditandai OB.\n";
        echo "Silakan tandai kontainer sebagai OB terlebih dahulu di halaman OB.\n";
    } elseif ($skipped > 0) {
        echo "ℹ️ Semua kontainer sudah memiliki manifest.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
