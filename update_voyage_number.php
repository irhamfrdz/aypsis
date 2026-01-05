<?php

/**
 * Script untuk mengubah nomor voyage ST09JP26 menjadi ST01JP01
 * pada table naik_kapal
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "  UPDATE NOMOR VOYAGE NAIK KAPAL\n";
echo "==============================================\n\n";

$oldVoyage = 'ST09JP26';
$newVoyage = 'ST01JP01';

try {
    DB::beginTransaction();

    // Cek data yang akan diubah
    $count = DB::table('naik_kapal')
        ->where('no_voyage', $oldVoyage)
        ->count();

    if ($count === 0) {
        echo "⚠️  Tidak ada data dengan nomor voyage '{$oldVoyage}'\n";
        echo "\nScript selesai tanpa perubahan.\n";
        exit(0);
    }

    echo "Ditemukan {$count} data dengan nomor voyage '{$oldVoyage}'\n\n";
    echo "Data yang akan diubah:\n";
    echo "─────────────────────────────────────────────\n";

    // Tampilkan data yang akan diubah
    $records = DB::table('naik_kapal')
        ->where('no_voyage', $oldVoyage)
        ->get();

    foreach ($records as $record) {
        echo "ID: {$record->id} | Voyage: {$record->no_voyage}\n";
    }

    echo "─────────────────────────────────────────────\n\n";
    echo "Nomor voyage akan diubah dari: {$oldVoyage}\n";
    echo "Nomor voyage baru            : {$newVoyage}\n\n";

    // Konfirmasi
    echo "Lanjutkan perubahan? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    
    if (trim(strtolower($line)) !== 'yes' && trim(strtolower($line)) !== 'y') {
        echo "\n❌ Perubahan dibatalkan.\n";
        fclose($handle);
        exit(0);
    }

    fclose($handle);

    // Update data
    $updated = DB::table('naik_kapal')
        ->where('no_voyage', $oldVoyage)
        ->update([
            'no_voyage' => $newVoyage,
            'updated_at' => now(),
        ]);

    DB::commit();

    echo "\n==============================================\n";
    echo "  UPDATE BERHASIL!\n";
    echo "==============================================\n\n";

    echo "✅ {$updated} data berhasil diubah\n";
    echo "✅ Nomor voyage dari '{$oldVoyage}' menjadi '{$newVoyage}'\n\n";

    // Tampilkan hasil
    echo "Verifikasi hasil:\n";
    echo "─────────────────────────────────────────────\n";
    
    $updatedRecords = DB::table('naik_kapal')
        ->where('no_voyage', $newVoyage)
        ->get();

    foreach ($updatedRecords as $record) {
        echo "ID: {$record->id} | Voyage: {$record->no_voyage}\n";
    }
    echo "─────────────────────────────────────────────\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n==============================================\n";
echo "  SCRIPT SELESAI\n";
echo "==============================================\n";
