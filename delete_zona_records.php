<?php
/**
 * Script untuk menghapus daftar tagihan kontainer dengan vendor ZONA
 * WARNING: Script ini akan menghapus data secara permanen!
 */

echo "=== DELETE ZONA VENDOR RECORDS ===\n\n";

// Include Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

// Safety check - require confirmation
echo "⚠️  WARNING: Script ini akan menghapus SEMUA record dengan vendor ZONA!\n";
echo "Data yang dihapus tidak dapat dikembalikan.\n\n";

// Count records first
try {
    $zonaCount = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->count();
    
    if ($zonaCount == 0) {
        echo "✅ Tidak ada record dengan vendor ZONA yang ditemukan.\n";
        exit(0);
    }
    
    echo "📊 Ditemukan $zonaCount record dengan vendor ZONA\n\n";
    
    // Show sample records
    echo "Sample 5 record yang akan dihapus:\n";
    $sampleRecords = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
        ->select(['id', 'vendor', 'nomor_kontainer', 'tanggal_awal', 'tanggal_akhir', 'adjustment', 'dpp'])
        ->limit(5)
        ->get();
    
    foreach ($sampleRecords as $record) {
        echo "ID: {$record->id} | Container: {$record->nomor_kontainer} | ";
        echo "Period: {$record->tanggal_awal} to {$record->tanggal_akhir} | ";
        echo "Adjustment: {$record->adjustment} | DPP: {$record->dpp}\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "Apakah Anda yakin ingin menghapus $zonaCount record dengan vendor ZONA?\n";
    echo "Ketik 'YES DELETE ZONA' untuk konfirmasi atau tekan Enter untuk batal: ";
    
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
    
    if ($confirmation !== 'YES DELETE ZONA') {
        echo "\n❌ Operasi dibatalkan. Tidak ada data yang dihapus.\n";
        exit(0);
    }
    
    echo "\n🔄 Memulai proses penghapusan...\n";
    
    // Begin transaction for safety
    DB::beginTransaction();
    
    try {
        // Log before deletion
        $logData = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')
            ->select(['id', 'nomor_kontainer', 'vendor', 'dpp', 'adjustment', 'created_at'])
            ->get()
            ->toArray();
        
        // Save deletion log
        $logFile = 'zona_deletion_log_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($logFile, json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'total_records' => $zonaCount,
            'deleted_records' => $logData
        ], JSON_PRETTY_PRINT));
        
        echo "📝 Log penghapusan disimpan ke: $logFile\n";
        
        // Perform deletion
        $deletedCount = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->delete();
        
        // Commit transaction
        DB::commit();
        
        echo "✅ Berhasil menghapus $deletedCount record dengan vendor ZONA\n";
        echo "📋 Detail penghapusan tersimpan di file: $logFile\n\n";
        
        // Verify deletion
        $remainingCount = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->count();
        if ($remainingCount == 0) {
            echo "✅ Verifikasi: Tidak ada lagi record vendor ZONA di database\n";
        } else {
            echo "⚠️  Warning: Masih ada $remainingCount record vendor ZONA tersisa\n";
        }
        
    } catch (Exception $e) {
        // Rollback on error
        DB::rollback();
        echo "❌ Error during deletion: " . $e->getMessage() . "\n";
        echo "🔄 Transaction rolled back - tidak ada data yang dihapus\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== SELESAI ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";