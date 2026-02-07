<?php

/**
 * Script untuk membuat manifest untuk voyage SA01JP26
 * Memeriksa semua kontainer yang sudah OB untuk voyage ini
 * 
 * Cara menjalankan:
 * php fix_manifests_sa01jp26.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

echo "===== FIX MANIFESTS - VOYAGE SA01JP26 =====\n";
echo "Memeriksa kontainer untuk voyage SA01JP26...\n\n";

try {
    DB::beginTransaction();
    
    $targetVoyage = 'SA01JP26';
    
    // Cari semua naik_kapal untuk voyage ini yang sudah OB
    $naikKapals = NaikKapal::where('no_voyage', $targetVoyage)
        ->where('sudah_ob', true)
        ->whereNotNull('nomor_kontainer')
        ->whereNotNull('nama_kapal')
        ->with('prospek.tandaTerima')
        ->get();
    
    echo "Ditemukan " . $naikKapals->count() . " kontainer yang sudah OB untuk voyage $targetVoyage\n\n";
    
    if ($naikKapals->count() === 0) {
        echo "⚠️ TIDAK ADA DATA yang sudah OB untuk voyage $targetVoyage\n";
        DB::rollBack();
        return;
    }
    
    $created = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($naikKapals as $naikKapal) {
        echo "═══════════════════════════════════════════\n";
        echo "ID: {$naikKapal->id}\n";
        echo "Kontainer: {$naikKapal->nomor_kontainer}\n";
        echo "Tipe: {$naikKapal->tipe_kontainer}\n";
        echo "Kapal: {$naikKapal->nama_kapal}\n";
        echo "Voyage: {$naikKapal->no_voyage}\n";
        echo "Jenis Barang: " . ($naikKapal->jenis_barang ?? '-') . "\n";
        
        // Untuk CARGO, gunakan nomor kontainer unik berdasarkan ID
        $nomorKontainer = $naikKapal->nomor_kontainer;
        if (strtoupper($naikKapal->tipe_kontainer) === 'CARGO') {
            $nomorKontainer = "CARGO-{$naikKapal->id}";
            echo "Nomor Kontainer Unik: {$nomorKontainer}\n";
        }
        
        // Khusus untuk LCL: buat manifest berdasarkan tanda terima
        if (strtoupper($naikKapal->tipe_kontainer) === 'LCL') {
            echo "→ LCL detected, mencari tanda terima...\n";
            
            // Cari semua tanda terima yang terhubung dengan kontainer ini
            $tandaTerimaRecords = \App\Models\TandaTerimaLclKontainerPivot::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                ->with('tandaTerima.items')
                ->get();
            
            if ($tandaTerimaRecords->count() > 0) {
                echo "  Ditemukan " . $tandaTerimaRecords->count() . " tanda terima\n";
                
                foreach ($tandaTerimaRecords as $pivot) {
                    $tandaTerima = $pivot->tandaTerima;
                    if (!$tandaTerima) continue;
                    
                    echo "  ├─ TT: {$tandaTerima->nomor_tanda_terima}\n";
                    
                    // Cek duplikasi manifest
                    $existingManifest = Manifest::where('nomor_kontainer', $naikKapal->nomor_kontainer)
                        ->where('no_voyage', $naikKapal->no_voyage)
                        ->where('nama_kapal', $naikKapal->nama_kapal)
                        ->where('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima)
                        ->first();
                    
                    if ($existingManifest) {
                        echo "  │  ✓ Manifest sudah ada (ID: {$existingManifest->id})\n";
                        $skipped++;
                        continue;
                    }
                    
                    try {
                        // Buat manifest untuk setiap tanda terima
                        $manifest = new Manifest();
                        
                        // Data kontainer
                        $manifest->nomor_kontainer = $naikKapal->nomor_kontainer;
                        $manifest->no_seal = $pivot->nomor_seal ?? $naikKapal->no_seal;
                        $manifest->tipe_kontainer = $naikKapal->tipe_kontainer;
                        $manifest->size_kontainer = $naikKapal->size_kontainer;
                        
                        // Data kapal & voyage
                        $manifest->nama_kapal = $naikKapal->nama_kapal;
                        $manifest->no_voyage = $naikKapal->no_voyage;
                        
                        // Data dari tanda terima
                        $manifest->nomor_tanda_terima = $tandaTerima->nomor_tanda_terima;
                        $manifest->pengirim = $tandaTerima->nama_pengirim;
                        $manifest->penerima = $tandaTerima->penerima;
                        $manifest->alamat_pengirim = $tandaTerima->alamat_pengirim;
                        $manifest->alamat_penerima = $tandaTerima->alamat_penerima;
                        
                        // Nama barang dari items
                        $namaBarang = $tandaTerima->items->pluck('nama_barang')->filter()->implode(', ');
                        $manifest->nama_barang = $namaBarang ?: $naikKapal->jenis_barang;
                        
                        // Volume dan tonnage dari items
                        $manifest->volume = $tandaTerima->items->sum('meter_kubik');
                        $manifest->tonnage = $tandaTerima->items->sum('tonase');
                        
                        // Pelabuhan
                        $manifest->pelabuhan_muat = $naikKapal->asal_kontainer;
                        $manifest->pelabuhan_bongkar = $naikKapal->ke;
                        
                        // Tanggal
                        $manifest->tanggal_berangkat = $naikKapal->tanggal_ob ?? now();
                        $manifest->penerimaan = $tandaTerima->tanggal_tanda_terima;
                        
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
                        
                        // Referensi prospek
                        if ($naikKapal->prospek_id) {
                            $manifest->prospek_id = $naikKapal->prospek_id;
                        }
                        
                        $manifest->created_by = 1;
                        $manifest->updated_by = 1;
                        
                        $manifest->save();
                        
                        echo "  │  ✅ Manifest dibuat: {$manifest->nomor_bl}\n";
                        $created++;
                        
                    } catch (\Exception $e) {
                        echo "  │  ❌ Error: " . $e->getMessage() . "\n";
                        $errors++;
                    }
                }
            } else {
                echo "  ⚠️ Tidak ada tanda terima, skip\n";
                $skipped++;
            }
        } else {
            // Untuk FCL dan CARGO: buat 1 manifest per kontainer
            
            // Cek apakah sudah ada manifest
            $existingManifest = Manifest::where('nomor_kontainer', $nomorKontainer)
                ->where('no_voyage', $naikKapal->no_voyage)
                ->where('nama_kapal', $naikKapal->nama_kapal)
                ->first();
            
            if ($existingManifest) {
                echo "✓ Manifest sudah ada\n";
                echo "  ID: {$existingManifest->id}\n";
                echo "  Nomor: {$existingManifest->nomor_bl}\n";
                $skipped++;
                continue;
            }
            
            try {
                echo "→ Membuat manifest baru...\n";
                
                // Buat manifest baru
                $manifest = new Manifest();
                $manifest->nomor_kontainer = $nomorKontainer;
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
                $errors++;
            }
        }
        
        echo "\n";
    }
    
    DB::commit();
    
    echo "═══════════════════════════════════════════\n";
    echo "===== RINGKASAN =====\n";
    echo "Voyage: $targetVoyage\n";
    echo "Total kontainer yang sudah OB: " . $naikKapals->count() . "\n";
    echo "Manifest dibuat: $created\n";
    echo "Sudah ada (dilewati): $skipped\n";
    echo "Error: $errors\n\n";
    
    if ($created > 0) {
        echo "✅ Berhasil membuat $created manifest baru untuk voyage $targetVoyage!\n";
    } elseif ($skipped > 0) {
        echo "ℹ️ Semua kontainer sudah memiliki manifest.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
