<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "  IMPORT SEMUA DATA MASTER DARI BACKUP SQL\n";
echo "==============================================\n\n";

// Daftar tabel master yang akan diimpor
$masterTables = [
    'master_kegiatans',
    'master_pricelist_sewa_kontainers',
    'divisis',
    'pekerjaans',
    'pajaks',
    'banks',
    'akun_coa',
    'cabangs',
    'vendor_bengkels',
    'tipe_akuns',
    'kode_nomor',
    'pricelist_cats',
    'stock_kontainers',
    'nomor_terakhir',
];

try {
    // Baca file SQL backup
    $sqlFile = 'c:\folder_kerjaan\backup\aypsis_backup.sql';
    
    if (!file_exists($sqlFile)) {
        die("File backup tidak ditemukan: $sqlFile\n");
    }
    
    echo "Membaca file backup...\n";
    $content = file_get_contents($sqlFile);
    echo "File backup berhasil dibaca (" . number_format(strlen($content)) . " bytes)\n\n";
    
    $successCount = 0;
    $failedCount = 0;
    $results = [];
    
    foreach ($masterTables as $table) {
        echo "Processing: $table\n";
        echo str_repeat("-", 50) . "\n";
        
        try {
            // Cari INSERT statement untuk tabel ini
            $pattern = '/INSERT INTO `' . preg_quote($table, '/') . '` VALUES (.+?);/s';
            
            if (preg_match($pattern, $content, $matches)) {
                $insertData = $matches[1];
                
                echo "  ✓ Data ditemukan\n";
                echo "  • Menghapus data lama...\n";
                
                // Hapus data lama
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table($table)->truncate();
                
                echo "  • Mengimpor data baru...\n";
                
                // Execute insert statement
                $sql = "INSERT INTO `$table` VALUES " . $insertData;
                DB::statement($sql);
                
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                $count = DB::table($table)->count();
                echo "  ✓ Berhasil! Total $count record diimpor\n";
                
                $results[$table] = [
                    'status' => 'success',
                    'count' => $count
                ];
                $successCount++;
                
            } else {
                echo "  ⚠ Data INSERT tidak ditemukan dalam file backup\n";
                $results[$table] = [
                    'status' => 'not_found',
                    'count' => 0
                ];
                $failedCount++;
            }
            
        } catch (Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
            $results[$table] = [
                'status' => 'error',
                'count' => 0,
                'error' => $e->getMessage()
            ];
            $failedCount++;
        }
        
        echo "\n";
    }
    
    // Summary
    echo "==============================================\n";
    echo "  RINGKASAN IMPORT\n";
    echo "==============================================\n\n";
    
    foreach ($results as $table => $result) {
        $status = $result['status'];
        $count = $result['count'];
        
        if ($status === 'success') {
            echo "✓ $table: $count record\n";
        } elseif ($status === 'not_found') {
            echo "⚠ $table: Tidak ditemukan dalam backup\n";
        } else {
            echo "✗ $table: Error - " . ($result['error'] ?? 'Unknown error') . "\n";
        }
    }
    
    echo "\n";
    echo "Total Berhasil: $successCount tabel\n";
    echo "Total Gagal/Tidak Ditemukan: $failedCount tabel\n";
    echo "==============================================\n";
    
} catch (Exception $e) {
    echo "\n✗ Fatal Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
