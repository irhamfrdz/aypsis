<?php
/**
 * Script untuk membuat manifest untuk voyage ST01JP26
 * Memeriksa semua kontainer yang sudah OB untuk voyage ini
 * 
 * Cara menjalankan:
 * php fix_manifests_st01jp26.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

echo "===== FIX MANIFESTS - VOYAGE ST01JP26 =====\n";
echo "Memeriksa kontainer untuk voyage ST01JP26...\n\n";

try {
    DB::beginTransaction();
    
    $targetVoyage = 'ST01JP26';
    
    // Ambil semua naik_kapal untuk voyage ST01JP26 yang sudah OB
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

    $manifestDibuat = 0;
    $sudahAda = 0;
    $error = 0;

    foreach ($naikKapals as $nk) {
        echo "═══════════════════════════════════════════\n";
        echo "ID: {$nk->id}\n";
        echo "Kontainer: {$nk->nomor_kontainer}\n";
        echo "Tipe: {$nk->tipe_kontainer}\n";
        echo "Kapal: {$nk->nama_kapal}\n";
        echo "Voyage: {$nk->no_voyage}\n";
        echo "Jenis Barang: " . ($nk->jenis_barang ?? '-') . "\n";

        // Tentukan nomor kontainer yang akan dicek
        $nomorKontainerCek = $nk->nomor_kontainer;
        if (strtoupper($nk->tipe_kontainer) === 'CARGO') {
            // Untuk CARGO, gunakan nomor unik
            $nomorKontainerCek = "CARGO-{$nk->id}";
            echo "Nomor Kontainer Unik: {$nomorKontainerCek}\n";
        }

        // Khusus untuk LCL: buat manifest berdasarkan tanda terima
        if (strtoupper($nk->tipe_kontainer) === 'LCL') {
            echo "→ LCL detected, mencari tanda terima...\n";
            
            // Cari semua tanda terima yang terhubung dengan kontainer ini
            $tandaTerimaRecords = \App\Models\TandaTerimaLclKontainerPivot::where('nomor_kontainer', $nk->nomor_kontainer)
                ->with('tandaTerima.items')
                ->get();
            
            if ($tandaTerimaRecords->count() > 0) {
                echo "  Ditemukan " . $tandaTerimaRecords->count() . " tanda terima\n";
                
                foreach ($tandaTerimaRecords as $pivot) {
                    $tandaTerima = $pivot->tandaTerima;
                    if (!$tandaTerima) continue;
                    
                    echo "  ├─ TT: {$tandaTerima->nomor_tanda_terima}\n";
                    
                    // Cek duplikasi manifest
                    $existingManifest = Manifest::where('nomor_kontainer', $nk->nomor_kontainer)
                        ->where('no_voyage', $nk->no_voyage)
                        ->where('nama_kapal', $nk->nama_kapal)
                        ->where('nomor_tanda_terima', $tandaTerima->nomor_tanda_terima)
                        ->first();
                    
                    if ($existingManifest) {
                        echo "  │  ✓ Manifest sudah ada (ID: {$existingManifest->id})\n";
                        $sudahAda++;
                        continue;
                    }
                    
                    try {
                        // Buat manifest untuk setiap tanda terima
                        $manifest = new Manifest();
                        
                        // Data kontainer
                        $manifest->nomor_kontainer = $nk->nomor_kontainer;
                        $manifest->no_seal = $pivot->nomor_seal ?? $nk->no_seal;
                        $manifest->tipe_kontainer = $nk->tipe_kontainer;
                        $manifest->size_kontainer = $nk->size_kontainer;
                        
                        // Data kapal & voyage
                        $manifest->nama_kapal = $nk->nama_kapal;
                        $manifest->no_voyage = $nk->no_voyage;
                        
                        // Data dari tanda terima
                        $manifest->nomor_tanda_terima = $tandaTerima->nomor_tanda_terima;
                        $manifest->pengirim = $tandaTerima->nama_pengirim;
                        $manifest->penerima = $tandaTerima->penerima;
                        $manifest->alamat_pengirim = $tandaTerima->alamat_pengirim;
                        $manifest->alamat_penerima = $tandaTerima->alamat_penerima;
                        
                        // Nama barang dari items
                        $namaBarang = $tandaTerima->items->pluck('nama_barang')->filter()->implode(', ');
                        $manifest->nama_barang = $namaBarang ?: $nk->jenis_barang;
                        
                        // Volume dan tonnage dari items
                        $manifest->volume = $tandaTerima->items->sum('meter_kubik');
                        $manifest->tonnage = $tandaTerima->items->sum('tonase');
                        
                        // Pelabuhan
                        $manifest->pelabuhan_muat = $nk->asal_kontainer;
                        $manifest->pelabuhan_bongkar = $nk->ke;
                        
                        // Tanggal
                        $manifest->tanggal_berangkat = $nk->tanggal_ob ?? now();
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
                        if ($nk->prospek_id) {
                            $manifest->prospek_id = $nk->prospek_id;
                        }
                        
                        $manifest->created_by = 1;
                        $manifest->updated_by = 1;
                        
                        $manifest->save();
                        
                        echo "  │  ✅ Manifest dibuat: {$manifest->nomor_bl}\n";
                        $manifestDibuat++;
                        
                    } catch (\Exception $e) {
                        echo "  │  ❌ Error: " . $e->getMessage() . "\n";
                        $error++;
                    }
                }
            } else {
                echo "  ⚠️ Tidak ada tanda terima, skip\n";
                $sudahAda++;
            }
        } else {
            // Untuk FCL dan CARGO: buat 1 manifest per kontainer
            
            // Cek apakah manifest sudah ada
            $existingManifest = Manifest::where('nomor_kontainer', $nomorKontainerCek)
                ->where('no_voyage', $nk->no_voyage)
                ->where('nama_kapal', $nk->nama_kapal)
                ->first();

            if ($existingManifest) {
                echo "✓ Manifest sudah ada\n";
                echo "  ID: {$existingManifest->id}\n";
                echo "  Nomor: {$existingManifest->nomor_bl}\n";
                $sudahAda++;
                continue;
            }

            try {
                echo "→ Membuat manifest baru...\n";
                
                // Buat manifest baru
                $manifest = new Manifest();
                $manifest->nomor_kontainer = $nomorKontainerCek;
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
                
                // Data pengirim/penerima dari prospek jika ada
                if ($nk->prospek_id && $nk->prospek) {
                    $manifest->prospek_id = $nk->prospek_id;
                    $manifest->pengirim = $nk->prospek->pt_pengirim;
                    
                    $penerima = null;
                    if ($nk->prospek->tandaTerima) {
                        $penerima = $nk->prospek->tandaTerima->penerima;
                        $manifest->alamat_penerima = $nk->prospek->tandaTerima->alamat_penerima;
                    }
                    $manifest->penerima = $penerima ?? $nk->prospek->tujuan_pengiriman;
                    
                    echo "  Prospek ID: {$nk->prospek_id}\n";
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
                $manifestDibuat++;

            } catch (\Exception $e) {
                echo "❌ Error: " . $e->getMessage() . "\n";
                $error++;
            }
        }
        
        echo "\n";
    }
    
    DB::commit();

    echo "═══════════════════════════════════════════\n";
    echo "===== RINGKASAN =====\n";
    echo "Voyage: $targetVoyage\n";
    echo "Total kontainer yang sudah OB: " . $naikKapals->count() . "\n";
    echo "Manifest dibuat: {$manifestDibuat}\n";
    echo "Sudah ada (dilewati): {$sudahAda}\n";
    echo "Error: {$error}\n\n";

    if ($manifestDibuat > 0) {
        echo "✅ Berhasil membuat {$manifestDibuat} manifest baru untuk voyage $targetVoyage!\n";
    } elseif ($sudahAda > 0) {
        echo "ℹ️ Semua kontainer sudah memiliki manifest.\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
