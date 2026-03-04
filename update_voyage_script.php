<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Script untuk mengubah no_voyage pada table bls dan manifests
 * Dari: 'SA04BJ25' Menjadi: 'SA04BJ26'
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oldVoyage = 'SA04BJ25';
$newVoyage = 'SA04BJ26';

echo "Memulai update no_voyage dari '$oldVoyage' ke '$newVoyage'...\n";

try {
    DB::beginTransaction();

    // 1. Update Tabel bls
    if (Schema::hasTable('bls')) {
        $affectedBls = DB::table('bls')
            ->where('no_voyage', $oldVoyage)
            ->update(['no_voyage' => $newVoyage]);
        echo "Tabel 'bls': $affectedBls baris diperbarui.\n";
    } else {
        echo "Tabel 'bls' tidak ditemukan.\n";
    }

    // 2. Update Tabel manifests
    if (Schema::hasTable('manifests')) {
        $affectedManifests = DB::table('manifests')
            ->where('no_voyage', $oldVoyage)
            ->update(['no_voyage' => $newVoyage]);
        echo "Tabel 'manifests': $affectedManifests baris diperbarui.\n";
    } else {
        echo "Tabel 'manifests' tidak ditemukan.\n";
    }

    DB::commit();
    echo "Update selesai dan berhasil di-commit.\n";

} catch (\Exception $e) {
    if (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
    echo "Update dibatalkan (rollback).\n";
}
