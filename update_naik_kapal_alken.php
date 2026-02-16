<?php

use App\Models\NaikKapal;
use Illuminate\Support\Facades\DB;

/**
 * Script untuk mengubah data nama_kapal dan no_voyage di table naik_kapal
 * 
 * Dari: 
 * - Nama Kapal: 'KM. ALKEN PRINCESS'
 * - No Voyage: 'AP03JB26'
 * 
 * Menjadi:
 * - Nama Kapal: 'KM Sentosa 18'
 * - No Voyage: 'ST04JB26'
 */

// Memasukkan autoloader Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Memulai proses update data naik_kapal...\n";

try {
    DB::beginTransaction();

    $oldKapal = 'KM. ALKEN PRINCESS';
    $oldVoyage = 'AP03JB26';
    $newKapal = 'KM Sentosa 18';
    $newVoyage = 'ST04JB26';

    // Mencari data yang akan diupdate
    $query = NaikKapal::where('nama_kapal', $oldKapal)
                      ->where('no_voyage', $oldVoyage);
    
    $count = $query->count();

    if ($count > 0) {
        $updated = $query->update([
            'nama_kapal' => $newKapal,
            'no_voyage' => $newVoyage
        ]);

        if ($updated) {
            DB::commit();
            echo "Berhasil memperbarui {$updated} data.\n";
            echo "Data diubah dari [{$oldKapal} | {$oldVoyage}] menjadi [{$newKapal} | {$newVoyage}].\n";
        } else {
            DB::rollBack();
            echo "Gagal melakukan update data.\n";
        }
    } else {
        DB::rollBack();
        echo "Data tidak ditemukan untuk Nama Kapal: '{$oldKapal}' dan Voyage: '{$oldVoyage}'.\n";
        echo "Pastikan penulisan nama kapal dan nomor voyage sudah tepat (termasuk titik dan spasi).\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}
