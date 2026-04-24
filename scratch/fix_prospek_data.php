<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\SuratJalan;
use App\Models\Prospek;
use App\Models\TagihanSupirVendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting Prospek data fix script...\n";

// 1. Identify Surat Jalans that should have Prospek records but might not
// - is_supir_customer = 1
// - has TagihanSupirVendor (identifies supir vendor)

$suratJalanIdsWithVendor = TagihanSupirVendor::pluck('surat_jalan_id')->toArray();

$targetSJs = SuratJalan::where(function($query) use ($suratJalanIdsWithVendor) {
    $query->where('is_supir_customer', 1)
          ->orWhereIn('id', $suratJalanIdsWithVendor);
})->get();

echo "Found " . $targetSJs->count() . " Surat Jalans with Supir Customer or Supir Vendor.\n";

$createdCount = 0;
$skippedCount = 0;

foreach ($targetSJs as $sj) {
    // Check if Prospek already exists for this Surat Jalan
    if (Prospek::where('surat_jalan_id', $sj->id)->exists()) {
        $skippedCount++;
        continue;
    }

    // Determine if it was supir vendor for the keterangan suffix
    $isVendor = in_array($sj->id, $suratJalanIdsWithVendor);
    $keteranganSuffix = $isVendor ? 'Supir Vendor' : 'Supir Customer';

    // Same logic as in SuratJalanController
    $jumlahKontainer = $sj->jumlah_kontainer ?? 1;
    $nomorKontainerArray = [];
    $noSealArray = [];

    if (!empty($sj->no_kontainer)) {
        $nomorKontainerArray = array_filter(array_map('trim', explode(',', $sj->no_kontainer)));
    }
    if (!empty($sj->no_seal)) {
        $noSealArray = array_filter(array_map('trim', explode(',', $sj->no_seal)));
    }

    DB::beginTransaction();
    try {
        for ($i = 0; $i < max(1, (int)$jumlahKontainer); $i++) {
            $nomorKontainerIni = isset($nomorKontainerArray[$i]) ? $nomorKontainerArray[$i] : null;
            $noSealIni = isset($noSealArray[$i]) ? $noSealArray[$i] : null;

            $prospekData = [
                'tanggal' => $sj->tanggal_surat_jalan ?? now(),
                'nama_supir' => $sj->supir,
                'barang' => $sj->jenis_barang ?? null,
                'pt_pengirim' => $sj->pengirim ?? null,
                'ukuran' => $sj->size ?? null,
                'tipe' => $sj->tipe_kontainer ?? null,
                'no_surat_jalan' => $sj->no_surat_jalan ?? null,
                'surat_jalan_id' => $sj->id,
                'nomor_kontainer' => $nomorKontainerIni,
                'no_seal' => $noSealIni,
                'tujuan_pengiriman' => $sj->tujuan_pengiriman ?? null,
                'nama_kapal' => null,
                'keterangan' => "Auto generated from Data Fix Script ({$keteranganSuffix}): " . ($sj->no_surat_jalan ?? '-'),
                'status' => Prospek::STATUS_AKTIF,
                'created_by' => $sj->created_by ?? 1,
                'updated_by' => $sj->updated_by ?? 1
            ];

            Prospek::create($prospekData);
            $createdCount++;
        }
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        echo "Error creating prospek for SJ ID {$sj->id}: " . $e->getMessage() . "\n";
    }
}

echo "Finished!\n";
echo "Total Created: $createdCount\n";
echo "Total Skipped (already exists): $skippedCount\n";
