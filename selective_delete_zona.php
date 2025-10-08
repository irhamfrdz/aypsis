<?php
/**
 * Script untuk menghapus daftar tagihan kontainer dengan vendor ZONA
 * Dengan opsi filtering untuk keamanan
 */

echo "=== SELECTIVE DELETE ZONA VENDOR RECORDS ===\n\n";

// Include Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "Opsi penghapusan:\n";
echo "1. Hapus SEMUA record vendor ZONA (712 records)\n";
echo "2. Hapus hanya record yang baru di-import hari ini\n";
echo "3. Hapus berdasarkan group tertentu\n";
echo "4. Hapus berdasarkan rentang tanggal\n";
echo "5. Cancel - tidak jadi hapus\n\n";

echo "Pilih opsi (1-5): ";
$handle = fopen("php://stdin", "r");
$option = trim(fgets($handle));

$query = DaftarTagihanKontainerSewa::where('vendor', 'ZONA');
$description = '';

switch ($option) {
    case '1':
        $description = 'SEMUA record vendor ZONA';
        break;
        
    case '2':
        $query = $query->whereDate('created_at', '>=', date('Y-m-d'));
        $description = 'record vendor ZONA yang dibuat hari ini';
        break;
        
    case '3':
        echo "Masukkan nama group (misal: Z010, Z51): ";
        $group = trim(fgets($handle));
        if (empty($group)) {
            echo "Group tidak boleh kosong!\n";
            exit(1);
        }
        $query = $query->where('group', $group);
        $description = "record vendor ZONA dengan group $group";
        break;
        
    case '4':
        echo "Masukkan tanggal awal (YYYY-MM-DD): ";
        $startDate = trim(fgets($handle));
        echo "Masukkan tanggal akhir (YYYY-MM-DD): ";
        $endDate = trim(fgets($handle));
        
        if (empty($startDate) || empty($endDate)) {
            echo "Tanggal tidak boleh kosong!\n";
            exit(1);
        }
        
        $query = $query->whereBetween('tanggal_awal', [$startDate, $endDate]);
        $description = "record vendor ZONA dengan tanggal $startDate s/d $endDate";
        break;
        
    case '5':
        echo "Operasi dibatalkan.\n";
        exit(0);
        
    default:
        echo "Opsi tidak valid!\n";
        exit(1);
}

fclose($handle);

try {
    // Count records to delete
    $recordsToDelete = $query->count();
    
    if ($recordsToDelete == 0) {
        echo "✅ Tidak ada record yang sesuai kriteria untuk dihapus.\n";
        exit(0);
    }
    
    echo "\n📊 Ditemukan $recordsToDelete $description\n\n";
    
    // Show financial impact
    $financialSummary = $query->selectRaw('
        SUM(dpp) as total_dpp, 
        SUM(adjustment) as total_adjustment,
        SUM(grand_total) as total_grand_total
    ')->first();
    
    echo "💰 Dampak finansial penghapusan:\n";
    echo "  Total DPP: Rp " . number_format($financialSummary->total_dpp ?? 0, 2) . "\n";
    echo "  Total Adjustment: Rp " . number_format($financialSummary->total_adjustment ?? 0, 2) . "\n";
    echo "  Total Grand Total: Rp " . number_format($financialSummary->total_grand_total ?? 0, 2) . "\n\n";
    
    // Show sample records
    echo "Sample 5 record yang akan dihapus:\n";
    $sampleRecords = $query->limit(5)->get(['id', 'nomor_kontainer', 'group', 'dpp', 'adjustment', 'created_at']);
    
    foreach ($sampleRecords as $record) {
        echo "ID: {$record->id} | Container: {$record->nomor_kontainer} | Group: {$record->group} | ";
        echo "DPP: " . number_format($record->dpp) . " | Adjustment: " . number_format($record->adjustment) . " | ";
        echo "Created: {$record->created_at}\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n";
    echo "⚠️  PERINGATAN: Anda akan menghapus $recordsToDelete record!\n";
    echo "Data yang dihapus tidak dapat dikembalikan.\n\n";
    echo "Ketik 'DELETE CONFIRMED' untuk melanjutkan atau Enter untuk batal: ";
    
    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);
    
    if ($confirmation !== 'DELETE CONFIRMED') {
        echo "\n❌ Operasi dibatalkan. Tidak ada data yang dihapus.\n";
        exit(0);
    }
    
    echo "\n🔄 Memulai proses penghapusan...\n";
    
    // Begin transaction
    DB::beginTransaction();
    
    try {
        // Create backup log
        $recordsData = $query->get()->toArray();
        
        $logFile = 'zona_deletion_log_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($logFile, json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'deletion_criteria' => $description,
            'total_records' => $recordsToDelete,
            'financial_impact' => $financialSummary->toArray(),
            'deleted_records' => $recordsData
        ], JSON_PRETTY_PRINT));
        
        echo "📝 Backup log dibuat: $logFile\n";
        
        // Perform deletion
        $deletedCount = $query->delete();
        
        // Commit transaction
        DB::commit();
        
        echo "✅ Berhasil menghapus $deletedCount record\n";
        echo "📋 Backup data tersimpan di: $logFile\n\n";
        
        // Verify deletion
        $remainingCount = DaftarTagihanKontainerSewa::where('vendor', 'ZONA')->count();
        echo "📊 Sisa record vendor ZONA: $remainingCount\n";
        
    } catch (Exception $e) {
        DB::rollback();
        echo "❌ Error during deletion: " . $e->getMessage() . "\n";
        echo "🔄 Transaction rolled back - tidak ada data yang dihapus\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== PENGHAPUSAN SELESAI ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";