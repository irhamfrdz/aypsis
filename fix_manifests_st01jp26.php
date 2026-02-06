<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

echo "===== FIX MANIFESTS - VOYAGE ST01JP26 =====\n";
echo "Memeriksa kontainer untuk voyage ST01JP26...\n\n";

try {
    // Ambil semua naik_kapal untuk voyage ST01JP26 yang sudah OB
    $naikKapals = NaikKapal::where('no_voyage', 'ST01JP26')
        ->where('sudah_ob', true)
        ->with('prospek.tandaTerima')
        ->get();

    echo "Ditemukan " . $naikKapals->count() . " kontainer yang sudah OB untuk voyage ST01JP26\n\n";

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
        echo "Jenis Barang: {$nk->jenis_barang}\n";

        // Tentukan nomor kontainer yang akan dicek
        $nomorKontainerCek = $nk->nomor_kontainer;
        if (strtoupper($nk->nomor_kontainer) === 'CARGO' || $nk->tipe_kontainer === 'CARGO') {
            // Untuk CARGO, gunakan nomor unik
            $nomorKontainerCek = "CARGO-{$nk->id}";
            echo "Nomor Kontainer Unik: {$nomorKontainerCek}\n";
        }

        // Cek apakah manifest sudah ada
        $existingManifest = Manifest::where('nomor_kontainer', $nomorKontainerCek)
            ->where('no_voyage', $nk->no_voyage)
            ->first();

        if ($existingManifest) {
            echo "✓ Manifest sudah ada\n";
            echo "  ID: {$existingManifest->id}\n";
            echo "  Nomor: {$existingManifest->nomor_manifest}\n";
            $sudahAda++;
            continue;
        }

        // Buat manifest baru
        DB::beginTransaction();
        try {
            // Generate nomor manifest
            $lastManifest = Manifest::orderBy('id', 'desc')->first();
            $lastNumber = $lastManifest ? intval(substr($lastManifest->nomor_manifest, 4)) : 0;
            $newNumber = $lastNumber + 1;
            $nomorManifest = 'MNF-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

            $manifest = new Manifest();
            $manifest->nomor_manifest = $nomorManifest;
            $manifest->nomor_kontainer = $nomorKontainerCek;
            $manifest->no_voyage = $nk->no_voyage;
            $manifest->tipe_kontainer = $nk->tipe_kontainer;
            $manifest->nama_barang = $nk->jenis_barang;
            $manifest->nama_kapal = $nk->nama_kapal;
            $manifest->pelabuhan_asal = 'Jakarta';
            $manifest->no_seal = $nk->no_seal;
            $manifest->size_kontainer = $nk->size_kontainer;
            $manifest->volume = $nk->total_volume;
            $manifest->tonnage = $nk->total_tonase;
            $manifest->kuantitas = $nk->kuantitas;
            $manifest->pelabuhan_muat = $nk->asal_kontainer;
            $manifest->pelabuhan_bongkar = $nk->ke;
            $manifest->tanggal_berangkat = $nk->tanggal_ob ?? now();
            
            // Data dari prospek jika ada
            if ($nk->prospek_id && $nk->prospek) {
                $manifest->prospek_id = $nk->prospek_id;
                $manifest->pelabuhan_tujuan = $nk->prospek->tujuan_pengiriman;
                $manifest->pengirim = $nk->prospek->pt_pengirim;
                
                if ($nk->prospek->tandaTerima) {
                    $manifest->penerima = $nk->prospek->tandaTerima->penerima;
                    $manifest->alamat_penerima = $nk->prospek->tandaTerima->alamat_penerima;
                } else {
                    $manifest->penerima = $nk->prospek->tujuan_pengiriman;
                }
            }

            $manifest->save();

            DB::commit();

            echo "✅ Manifest berhasil dibuat!\n";
            echo "  ID: {$manifest->id}\n";
            echo "  Nomor: {$manifest->nomor_manifest}\n";
            $manifestDibuat++;

        } catch (\Exception $e) {
            DB::rollBack();
            echo "❌ Error: " . $e->getMessage() . "\n";
            $error++;
        }
    }

    echo "═══════════════════════════════════════════\n";
    echo "===== RINGKASAN =====\n";
    echo "Voyage: ST01JP26\n";
    echo "Total kontainer yang sudah OB: " . $naikKapals->count() . "\n";
    echo "Manifest dibuat: {$manifestDibuat}\n";
    echo "Sudah ada (dilewati): {$sudahAda}\n";
    echo "Error: {$error}\n\n";

    if ($manifestDibuat > 0) {
        echo "✅ Berhasil membuat {$manifestDibuat} manifest baru!\n";
    } else if ($sudahAda > 0 && $error == 0) {
        echo "ℹ️ Semua kontainer sudah memiliki manifest.\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
