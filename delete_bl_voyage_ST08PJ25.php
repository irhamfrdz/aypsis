<?php

/**
 * Script untuk menghapus data BL dengan nomor voyage ST08PJ25
 * 
 * Usage: php delete_bl_voyage_ST08PJ25.php
 * 
 * PERINGATAN: Script ini akan menghapus data secara permanen!
 * Pastikan sudah backup database sebelum menjalankan script ini.
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$voyageNumber = 'ST08PJ25';

echo "=================================================\n";
echo "Script Hapus Data BL - Voyage: {$voyageNumber}\n";
echo "=================================================\n\n";

echo "⚠️  PERINGATAN: Script ini akan menghapus data secara PERMANEN!\n";
echo "Pastikan Anda sudah backup database sebelum melanjutkan.\n\n";

// Cek data yang akan dihapus
echo "Mencari data BL dengan voyage {$voyageNumber}...\n\n";

$bls = DB::table('bl')
    ->where('no_voyage', $voyageNumber)
    ->get();

if ($bls->isEmpty()) {
    echo "❌ Tidak ada data BL dengan voyage {$voyageNumber}\n";
    exit(0);
}

echo "Ditemukan " . $bls->count() . " data BL:\n";
echo "----------------------------------------\n";
foreach ($bls as $index => $bl) {
    echo ($index + 1) . ". ID: {$bl->id} | Nomor BL: " . ($bl->nomor_bl ?? '-') . " | Kontainer: " . ($bl->nomor_kontainer ?? '-') . " | Kapal: " . ($bl->nama_kapal ?? '-') . "\n";
}
echo "----------------------------------------\n\n";

// Konfirmasi penghapusan
echo "Apakah Anda yakin ingin menghapus " . $bls->count() . " data BL ini? (yes/no): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'yes') {
    echo "\n❌ Penghapusan dibatalkan.\n";
    exit(0);
}

echo "\n🔄 Memulai proses penghapusan...\n";

try {
    DB::beginTransaction();
    
    $deletedCount = DB::table('bl')
        ->where('no_voyage', $voyageNumber)
        ->delete();
    
    DB::commit();
    
    echo "✅ Berhasil menghapus {$deletedCount} data BL dengan voyage {$voyageNumber}\n";
    echo "\n=================================================\n";
    echo "Proses selesai!\n";
    echo "=================================================\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Penghapusan dibatalkan.\n";
    exit(1);
}
