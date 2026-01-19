<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NaikKapal;
use App\Models\Manifest;
use Illuminate\Support\Facades\DB;

$voyage = 'SA01JP26';

echo "=== Full Resync: Delete All and Recreate ===" . PHP_EOL;
echo "Voyage: {$voyage}" . PHP_EOL . PHP_EOL;

// Step 1: Delete all existing manifests for this voyage
$deleteCount = Manifest::where('no_voyage', $voyage)->delete();
echo "Deleted existing manifests: {$deleteCount}" . PHP_EOL;

// Step 2: Get all naik_kapal records that are sudah_ob for this voyage
$naikKapals = NaikKapal::where('no_voyage', $voyage)
    ->where('sudah_ob', true)
    ->with('prospek')
    ->get();

echo "NaikKapal sudah OB to sync: " . $naikKapals->count() . PHP_EOL . PHP_EOL;

$created = 0;
$errors = 0;

foreach ($naikKapals as $nk) {
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
        echo ".";
    } catch (\Exception $e) {
        $errors++;
        echo PHP_EOL . "Error for {$nk->nomor_kontainer} (ID: {$nk->id}): " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL . PHP_EOL;
echo "=== Summary ===" . PHP_EOL;
echo "Created: {$created}" . PHP_EOL;
echo "Errors: {$errors}" . PHP_EOL;

// Final count
$finalCount = Manifest::where('no_voyage', $voyage)->count();
$nkOBCount = NaikKapal::where('no_voyage', $voyage)->where('sudah_ob', true)->count();

echo PHP_EOL . "NaikKapal (sudah_ob): {$nkOBCount}" . PHP_EOL;
echo "Manifest: {$finalCount}" . PHP_EOL;

if ($finalCount == $nkOBCount) {
    echo PHP_EOL . "✓ DATA SUDAH SINKRON!" . PHP_EOL;
} else {
    echo PHP_EOL . "✗ Selisih: " . abs($nkOBCount - $finalCount) . PHP_EOL;
}
