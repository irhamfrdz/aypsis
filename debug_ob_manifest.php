<?php

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check user reported IDs
$ids = [4944, 4945, 4946];

echo "DEBUGGING MANIFEST CREATION LOGIC\n";
echo "=================================\n";

foreach ($ids as $id) {
    try {
        $nk = NaikKapal::with('prospek')->find($id);

        if (!$nk) {
            echo "NaikKapal ID $id NOT FOUND. Skipping.\n";
            continue;
        }

        echo "\nChecking NaikKapal ID: $id\n";
        echo "Nomor Kontainer: '{$nk->nomor_kontainer}'\n";
        echo "Tipe Kontainer: '{$nk->tipe_kontainer}'\n";
        echo "Nama Kapal: '{$nk->nama_kapal}'\n";
        echo "Voyage: '{$nk->no_voyage}'\n";
        echo "Sudah OB: " . ($nk->sudah_ob ? 'YES' : 'NO') . "\n";
        echo "Details: Barang='{$nk->jenis_barang}', Vol='{$nk->total_volume}', Ton='{$nk->total_tonase}'\n";

        // Logic from controller
        $manifestDataForLater = [
            'tipe_kontainer' => $nk->tipe_kontainer,
            'nomor_kontainer' => $nk->nomor_kontainer,
            'no_seal' => $nk->no_seal,
            'size_kontainer' => $nk->size_kontainer,
            'nama_kapal' => $nk->nama_kapal,
            'no_voyage' => $nk->no_voyage,
            'jenis_barang' => $nk->jenis_barang,
            'total_volume' => $nk->total_volume,
            'total_tonase' => $nk->total_tonase,
            'asal_kontainer' => $nk->asal_kontainer,
            'ke' => $nk->ke,
            'prospek_id' => $nk->prospek_id,
            'prospek_pt_pengirim' => $nk->prospek ? $nk->prospek->pt_pengirim : null,
            'prospek_tujuan_pengiriman' => $nk->prospek ? $nk->prospek->tujuan_pengiriman : null,
        ];

        // LCL Check (simplified)
        if (strtoupper(trim($manifestDataForLater['tipe_kontainer'])) === 'LCL') {
            echo ">> Detected as LCL. Skipping.\n";
            continue;
        }

        echo ">> Entering FCL/CARGO Logic Block...\n";

        // Cek apakah CARGO (tipe atau nomor kontainer)
        $isCargo = (
            strtoupper(trim($manifestDataForLater['tipe_kontainer'] ?? '')) === 'CARGO' || 
            stripos($manifestDataForLater['nomor_kontainer'] ?? '', 'CARGO') !== false
        );
        
        echo ">> Is Cargo? " . ($isCargo ? "YES" : "NO") . "\n";

        // Cek apakah manifest sudah ada dengan detail yang sama
        $existingManifest = \App\Models\Manifest::where('nomor_kontainer', $manifestDataForLater['nomor_kontainer'])
            ->where('no_voyage', $manifestDataForLater['no_voyage'])
            ->where('nama_kapal', $manifestDataForLater['nama_kapal'])
            ->where('nama_barang', $manifestDataForLater['jenis_barang']) // Check exact match
            ->where('volume', $manifestDataForLater['total_volume'])       // Check exact match
            ->where('tonnage', $manifestDataForLater['total_tonase'])     // Check exact match
            ->first();
            
        if ($existingManifest) {
             echo ">> Found EXACT match manifest ID: {$existingManifest->id} (BL: {$existingManifest->nomor_bl})\n";
             echo ">> This NaikKapal ($id) is ALREADY processed successfully.\n";
        } else {
             echo ">> No EXACT match manifest found for this item.\n";
             echo ">> This NaikKapal ($id) needs a manifest created.\n";
             
             // Create manifest
             echo ">> Creating manifest for ID $id...\n";
             $manifest = new \App\Models\Manifest();
             $manifest->nomor_kontainer = $manifestDataForLater['nomor_kontainer'];
             $manifest->no_seal = $manifestDataForLater['no_seal'];
             $manifest->tipe_kontainer = $manifestDataForLater['tipe_kontainer'];
             $manifest->size_kontainer = $manifestDataForLater['size_kontainer'];
             $manifest->nama_kapal = $manifestDataForLater['nama_kapal'];
             $manifest->no_voyage = $manifestDataForLater['no_voyage'];
             $manifest->nama_barang = $manifestDataForLater['jenis_barang'];
             $manifest->volume = $manifestDataForLater['total_volume'];
             $manifest->tonnage = $manifestDataForLater['total_tonase'];
             $manifest->pelabuhan_muat = $manifestDataForLater['asal_kontainer'];
             $manifest->pelabuhan_bongkar = $manifestDataForLater['ke'];
             $manifest->tanggal_berangkat = now();

             if ($manifestDataForLater['prospek_id']) {
                 $manifest->prospek_id = $manifestDataForLater['prospek_id'];
                 $manifest->pengirim = $manifestDataForLater['prospek_pt_pengirim'];
                 $manifest->penerima = $manifestDataForLater['prospek_tujuan_pengiriman'];
             }

             $lastManifest = \App\Models\Manifest::whereNotNull('nomor_bl')->orderBy('id', 'desc')->first();
             if ($lastManifest && $lastManifest->nomor_bl) {
                 preg_match('/\d+/', $lastManifest->nomor_bl, $matches);
                 $lastNumber = isset($matches[0]) ? intval($matches[0]) : 0;
                 $manifest->nomor_bl = 'MNF-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
             } else {
                 $manifest->nomor_bl = 'MNF-000001';
             }

             $manifest->created_by = 1;
             $manifest->updated_by = 1;
             $manifest->save();

             echo ">> SUCCESS: Created manifest ID {$manifest->id} with BL {$manifest->nomor_bl}\n";
        }

    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
