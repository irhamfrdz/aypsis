<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

$voyage = 'SA01JP26';

echo "=== Sync All CARGO to Manifest (ignoring duplicates) ===" . PHP_EOL;
echo "Voyage: {$voyage}" . PHP_EOL . PHP_EOL;

// Get all naik_kapal records that are sudah_ob for this voyage
$naikKapals = NaikKapal::where('no_voyage', $voyage)
    ->where('sudah_ob', true)
    ->with('prospek')
    ->get();

echo "NaikKapal sudah OB: " . $naikKapals->count() . PHP_EOL;

// Get existing manifest IDs based on prospek_id (more reliable for CARGO)
$existingProspekIds = Manifest::where('no_voyage', $voyage)
    ->whereNotNull('prospek_id')
    ->pluck('prospek_id')
    ->toArray();

echo "Existing manifests with prospek_id: " . count($existingProspekIds) . PHP_EOL;

$created = 0;
$skipped = 0;
$errors = 0;

foreach ($naikKapals as $nk) {
    // Skip if manifest already exists for this prospek_id
    if ($nk->prospek_id && in_array($nk->prospek_id, $existingProspekIds)) {
        $skipped++;
        continue;
    }
    
    // For records without prospek_id, check by nomor_kontainer (but only for non-CARGO)
    if (!$nk->prospek_id && $nk->nomor_kontainer !== 'CARGO') {
        $exists = Manifest::where('no_voyage', $voyage)
            ->where('nomor_kontainer', $nk->nomor_kontainer)
            ->exists();
        if ($exists) {
            $skipped++;
            continue;
        }
    }

    try {
        $manifestData = [
            'nomor_kontainer' => $nk->nomor_kontainer,
            'no_seal' => $nk->no_seal,
            'tipe_kontainer' => $nk->tipe_kontainer,
            'size_kontainer' => $nk->size_kontainer,
            'nama_kapal' => $nk->nama_kapal,
            'no_voyage' => $nk->no_voyage,
            'pelabuhan_asal' => $nk->pelabuhan_asal,
            'pelabuhan_tujuan' => $nk->pelabuhan_tujuan,
            'nama_barang' => $nk->jenis_barang,
            'asal_kontainer' => $nk->asal_kontainer,
            'ke' => $nk->ke,
            'tonnage' => $nk->total_tonase,
            'volume' => $nk->total_volume,
            'kuantitas' => $nk->kuantitas ?? 1,
            'prospek_id' => $nk->prospek_id,
            'created_by' => $nk->created_by,
            'updated_by' => $nk->updated_by,
        ];

        if ($nk->prospek) {
            $prospek = $nk->prospek;
            $manifestData['pengirim'] = $prospek->pt_pengirim ?? $prospek->pengirim ?? null;
            $manifestData['alamat_pengirim'] = $prospek->alamat_pengirim ?? null;
            $manifestData['penerima'] = $prospek->pt_penerima ?? $prospek->penerima ?? null;
            $manifestData['alamat_penerima'] = $prospek->alamat_penerima ?? null;
            $manifestData['pelabuhan_muat'] = $prospek->port_muat ?? $nk->pelabuhan_asal ?? null;
            $manifestData['pelabuhan_bongkar'] = $prospek->port_bongkar ?? $nk->pelabuhan_tujuan ?? null;
        }

        Manifest::create($manifestData);
        $created++;
        
        // Add prospek_id to the existing list to prevent duplicates in this run
        if ($nk->prospek_id) {
            $existingProspekIds[] = $nk->prospek_id;
        }
        
        echo ".";
    } catch (\Exception $e) {
        $errors++;
        echo PHP_EOL . "Error for {$nk->nomor_kontainer} (ID: {$nk->id}): " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . PHP_EOL;
echo "=== Summary ===" . PHP_EOL;
echo "Created: {$created}" . PHP_EOL;
echo "Skipped (already exists): {$skipped}" . PHP_EOL;
echo "Errors: {$errors}" . PHP_EOL;

// Final count
$finalCount = Manifest::where('no_voyage', $voyage)->count();
echo PHP_EOL . "Final Manifest count for {$voyage}: {$finalCount}" . PHP_EOL;

// Compare
$nkOBCount = NaikKapal::where('no_voyage', $voyage)->where('sudah_ob', true)->count();
if ($finalCount == $nkOBCount) {
    echo "✓ DATA SUDAH SINKRON!" . PHP_EOL;
} else {
    echo "Selisih: " . abs($nkOBCount - $finalCount) . PHP_EOL;
}
