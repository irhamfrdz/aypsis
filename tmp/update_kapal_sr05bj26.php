<?php

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$noVoyage = 'SR05BJ26';
$namaKapalBaru = 'KM. SRIWIJAYA RAYA';

try {
    DB::beginTransaction();

    // 1. Update pada table bls
    $updatedBls = DB::table('bls')
        ->where('no_voyage', $noVoyage)
        ->update(['nama_kapal' => $namaKapalBaru]);

    // 2. Update pada table manifests
    // Asumsi kolom di tabel manifests juga bernama 'no_voyage' dan 'nama_kapal'
    $updatedManifests = DB::table('manifests')
        ->where('no_voyage', $noVoyage)
        ->update(['nama_kapal' => $namaKapalBaru]);

    DB::commit();

    echo "Update berhasil!\n";
    echo "--------------------------\n";
    echo "Jumlah data diganti di tabel bls: {$updatedBls} baris\n";
    echo "Jumlah data diganti di tabel manifests: {$updatedManifests} baris\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Terjadi kesalahan saat melakukan update: " . $e->getMessage() . "\n";
}
