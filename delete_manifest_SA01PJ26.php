<?php

/**
 * Script untuk menghapus data manifest dengan nomor voyage SA01PJ26
 * Jalankan: php delete_manifest_SA01PJ26.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Delete Manifest dengan Voyage SA01PJ26\n";
echo "===========================================\n\n";

try {
    // Cek jumlah record yang akan dihapus
    $count = DB::table('manifests')
        ->where('no_voyage', 'SA01PJ26')
        ->count();
    
    if ($count === 0) {
        echo "❌ Tidak ada data manifest dengan nomor voyage SA01PJ26\n";
        exit(0);
    }
    
    echo "📊 Ditemukan {$count} record manifest dengan nomor voyage SA01PJ26\n\n";
    
    // Tampilkan data yang akan dihapus
    $manifests = DB::table('manifests')
        ->where('no_voyage', 'SA01PJ26')
        ->select('id', 'no_voyage', 'nomor_bl', 'nomor_kontainer', 'tanggal_berangkat')
        ->get();
    
    echo "Data yang akan dihapus (sebagian kolom):\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-5s %-15s %-20s %-20s %-15s\n", 'ID', 'No Voyage', 'No BL', 'No Kontainer', 'Tgl Berangkat');
    echo str_repeat('-', 80) . "\n";
    
    foreach ($manifests as $manifest) {
        printf(
            "%-5s %-15s %-20s %-20s %-15s\n",
            $manifest->id,
            $manifest->no_voyage,
            $manifest->nomor_bl ?? '-',
            $manifest->nomor_kontainer ?? '-',
            $manifest->tanggal_berangkat ?? '-'
        );
    }
    echo str_repeat('-', 80) . "\n\n";
    
    // Konfirmasi penghapusan
    echo "⚠️  PERINGATAN: Data yang dihapus tidak dapat dikembalikan!\n";
    echo "Apakah Anda yakin ingin menghapus {$count} record ini? (yes/no): ";
    
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);
    
    if ($confirmation !== 'yes') {
        echo "\n❌ Penghapusan dibatalkan.\n";
        exit(0);
    }
    
    echo "\n🔄 Menghapus data...\n";
    
    // Hapus data
    $deleted = DB::table('manifests')
        ->where('no_voyage', 'SA01PJ26')
        ->delete();
    
    echo "\n✅ Berhasil menghapus {$deleted} record manifest dengan nomor voyage SA01PJ26\n";
    echo "===========================================\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
