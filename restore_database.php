<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== RESTORE DATABASE DAFTAR TAGIHAN KONTAINER SEWA ===" . PHP_EOL;
echo PHP_EOL;

// Baca konfigurasi database
$dbConfig = config('database.connections.mysql');

echo "Database: " . $dbConfig['database'] . PHP_EOL;
echo "Host: " . $dbConfig['host'] . PHP_EOL;
echo PHP_EOL;

// Backup data existing terlebih dahulu
echo "=== BACKUP DATA EXISTING ===" . PHP_EOL;

try {
    $existingCount = DB::table('daftar_tagihan_kontainer_sewa')->count();
    echo "Jumlah data existing: " . $existingCount . PHP_EOL;
    
    // Export existing data
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = "backup_daftar_tagihan_{$timestamp}.sql";
    
    $mysqldumpCmd = sprintf(
        'mysqldump -h%s -u%s -p%s %s daftar_tagihan_kontainer_sewa > %s',
        $dbConfig['host'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database'],
        $backupFile
    );
    
    echo "Membuat backup ke: " . $backupFile . PHP_EOL;
    exec($mysqldumpCmd, $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Backup berhasil dibuat" . PHP_EOL;
    } else {
        echo "⚠️ Warning: Backup mungkin gagal (return code: " . $returnCode . ")" . PHP_EOL;
    }
    
} catch (\Exception $e) {
    echo "❌ Error saat backup: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;
echo "=== MENGHAPUS DATA LAMA ===" . PHP_EOL;

try {
    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Truncate table
    DB::table('daftar_tagihan_kontainer_sewa')->truncate();
    
    echo "✅ Data lama berhasil dihapus" . PHP_EOL;
    
} catch (\Exception $e) {
    echo "❌ Error saat menghapus data: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL;
echo "=== RESTORE DATA BARU ===" . PHP_EOL;

try {
    // Read SQL file
    $sqlFile = 'aypsis1.sql';
    
    if (!file_exists($sqlFile)) {
        throw new \Exception("File SQL tidak ditemukan: " . $sqlFile);
    }
    
    echo "Membaca file: " . $sqlFile . PHP_EOL;
    $sql = file_get_contents($sqlFile);
    
    // Split by INSERT statements
    $statements = explode("INSERT INTO", $sql);
    
    $insertCount = 0;
    
    foreach ($statements as $index => $statement) {
        if ($index === 0) continue; // Skip header
        
        $statement = "INSERT INTO" . $statement;
        
        // Skip if not for daftar_tagihan_kontainer_sewa
        if (strpos($statement, 'daftar_tagihan_kontainer_sewa') === false) {
            continue;
        }
        
        try {
            DB::statement($statement);
            $insertCount++;
            
            if ($insertCount % 50 == 0) {
                echo "Progress: " . $insertCount . " records inserted..." . PHP_EOL;
            }
            
        } catch (\Exception $e) {
            echo "⚠️ Error inserting record " . $insertCount . ": " . $e->getMessage() . PHP_EOL;
        }
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "✅ Total " . $insertCount . " INSERT statements dijalankan" . PHP_EOL;
    
} catch (\Exception $e) {
    echo "❌ Error saat restore: " . $e->getMessage() . PHP_EOL;
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    exit(1);
}

echo PHP_EOL;
echo "=== VERIFIKASI ===" . PHP_EOL;

try {
    $newCount = DB::table('daftar_tagihan_kontainer_sewa')->count();
    echo "Jumlah data setelah restore: " . $newCount . PHP_EOL;
    
    // Sample data
    $samples = DB::table('daftar_tagihan_kontainer_sewa')
        ->orderBy('id')
        ->limit(5)
        ->get();
    
    echo PHP_EOL;
    echo "Sample data (5 record pertama):" . PHP_EOL;
    foreach ($samples as $sample) {
        echo "- ID: {$sample->id}, Kontainer: {$sample->nomor_kontainer}, Periode: {$sample->periode}, DPP: Rp " . number_format($sample->dpp, 2, '.', ',') . PHP_EOL;
    }
    
} catch (\Exception $e) {
    echo "❌ Error saat verifikasi: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;
echo "=== SELESAI ===" . PHP_EOL;
