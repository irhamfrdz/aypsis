<?php

/**
 * Script untuk mengupdate data BL yang kosong dengan data dari Prospek terkait
 * Jalankan: php update_bl_from_prospek.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Bl;
use App\Models\Prospek;
use App\Models\SuratJalan;
use App\Models\TandaTerima;
use Illuminate\Support\Facades\DB;

echo "=================================================\n";
echo "Script Update Data BL dari Prospek Terkait\n";
echo "=================================================\n\n";

// Ambil semua BL yang memiliki prospek_id
$bls = Bl::whereNotNull('prospek_id')
    ->with('prospek')
    ->get();

echo "Total BL dengan prospek_id: " . $bls->count() . "\n\n";

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($bls as $bl) {
    try {
        $prospek = $bl->prospek;
        
        if (!$prospek) {
            echo "⚠️  BL ID {$bl->id} - Prospek tidak ditemukan, skip\n";
            $skipped++;
            continue;
        }
        
        $changes = [];
        
        // Update pengirim jika kosong
        if (empty($bl->pengirim) && !empty($prospek->pt_pengirim)) {
            $bl->pengirim = $prospek->pt_pengirim;
            $changes[] = 'pengirim';
        }
        
        // Update penerima jika kosong
        if (empty($bl->penerima) && !empty($prospek->tujuan_pengiriman)) {
            $bl->penerima = $prospek->tujuan_pengiriman;
            $changes[] = 'penerima';
        }
        
        // Update no_seal jika kosong
        if (empty($bl->no_seal) && !empty($prospek->no_seal)) {
            $bl->no_seal = $prospek->no_seal;
            $changes[] = 'no_seal';
        }
        
        // Update tonnage jika kosong
        if (empty($bl->tonnage) && !empty($prospek->total_ton)) {
            $bl->tonnage = $prospek->total_ton;
            $changes[] = 'tonnage';
        }
        
        // Update volume jika kosong
        if (empty($bl->volume) && !empty($prospek->total_volume)) {
            $bl->volume = $prospek->total_volume;
            $changes[] = 'volume';
        }
        
        // Update kuantitas jika kosong
        if (empty($bl->kuantitas) && !empty($prospek->kuantitas)) {
            $bl->kuantitas = $prospek->kuantitas;
            $changes[] = 'kuantitas';
        }
        
        // Update tanggal_berangkat jika kosong
        if (empty($bl->tanggal_berangkat) && !empty($prospek->tanggal_muat)) {
            $bl->tanggal_berangkat = $prospek->tanggal_muat;
            $changes[] = 'tanggal_berangkat';
        }
        
        // Update nama_barang jika kosong
        if (empty($bl->nama_barang) && !empty($prospek->barang)) {
            $bl->nama_barang = $prospek->barang;
            $changes[] = 'nama_barang';
        }
        
        // Update pelabuhan_asal jika kosong
        if (empty($bl->pelabuhan_asal) && !empty($prospek->pelabuhan_asal)) {
            $bl->pelabuhan_asal = $prospek->pelabuhan_asal;
            $changes[] = 'pelabuhan_asal';
        }
        
        // Update tipe_kontainer jika kosong
        if (empty($bl->tipe_kontainer) && !empty($prospek->tipe)) {
            $bl->tipe_kontainer = $prospek->tipe;
            $changes[] = 'tipe_kontainer';
        }
        
        // Update size_kontainer jika kosong
        if (empty($bl->size_kontainer) && !empty($prospek->ukuran)) {
            $bl->size_kontainer = $prospek->ukuran;
            $changes[] = 'size_kontainer';
        }
        
        // Coba ambil data dari Surat Jalan jika ada
        if ($prospek->surat_jalan_id) {
            $suratJalan = SuratJalan::find($prospek->surat_jalan_id);
            if ($suratJalan) {
                // Update alamat_pengiriman jika kosong
                if (empty($bl->alamat_pengiriman) && !empty($suratJalan->alamat_tujuan)) {
                    $bl->alamat_pengiriman = $suratJalan->alamat_tujuan;
                    $changes[] = 'alamat_pengiriman (dari SJ)';
                }
                
                // Update contact_person jika kosong
                if (empty($bl->contact_person) && !empty($suratJalan->contact_person)) {
                    $bl->contact_person = $suratJalan->contact_person;
                    $changes[] = 'contact_person (dari SJ)';
                }
            }
        }
        
        // Coba ambil data dari Tanda Terima jika alamat_pengiriman masih kosong
        if (empty($bl->alamat_pengiriman) && $prospek->tanda_terima_id) {
            $tandaTerima = TandaTerima::find($prospek->tanda_terima_id);
            if ($tandaTerima) {
                // Update alamat_pengiriman jika kosong
                if (!empty($tandaTerima->alamat_penerima)) {
                    $bl->alamat_pengiriman = $tandaTerima->alamat_penerima;
                    $changes[] = 'alamat_pengiriman (dari TT)';
                }
                
                // Update contact_person jika kosong
                if (empty($bl->contact_person) && !empty($tandaTerima->contact_person)) {
                    $bl->contact_person = $tandaTerima->contact_person;
                    $changes[] = 'contact_person (dari TT)';
                }
            }
        }
        
        // Simpan jika ada perubahan
        if (count($changes) > 0) {
            $bl->save();
            echo "✅ BL ID {$bl->id} (Kontainer: {$bl->nomor_kontainer}) - Updated: " . implode(', ', $changes) . "\n";
            $updated++;
        } else {
            $skipped++;
        }
        
    } catch (\Exception $e) {
        echo "❌ BL ID {$bl->id} - Error: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=================================================\n";
echo "Selesai!\n";
echo "=================================================\n";
echo "Total BL diupdate: {$updated}\n";
echo "Total BL dilewati (sudah lengkap): {$skipped}\n";
echo "Total error: {$errors}\n";
echo "=================================================\n";
