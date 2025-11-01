<?php

/**
 * Script untuk mengimpor data dari aypsis.sql 
 * kecuali tabel users dan permissions yang ingin dipertahankan
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SELECTIVE DATA IMPORT SCRIPT ===\n";
echo "Mengimpor semua data dari aypsis.sql kecuali tabel users dan permissions\n\n";

// Daftar tabel yang TIDAK akan diimpor (dipertahankan yang sudah ada)
$excludedTables = [
    'users',
    'permissions',
    'roles',
    'model_has_permissions',
    'model_has_roles', 
    'role_has_permissions',
    'migrations', // Skip migrations karena sudah ada
    'failed_jobs', // Skip jobs
    'jobs',
    'cache',
    'cache_locks',
    'password_reset_tokens'
];

try {
    echo "1. Membaca file SQL...\n";
    $sqlFile = __DIR__ . '/aypsis.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("File aypsis.sql tidak ditemukan!");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    
    echo "2. Memproses dan memfilter SQL statements...\n";
    
    // Split menjadi statements individual
    $statements = explode(';', $sqlContent);
    
    $processedStatements = [];
    $currentTable = null;
    $skipCurrentTable = false;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        if (empty($statement)) continue;
        
        // Deteksi DROP TABLE
        if (preg_match('/DROP TABLE IF EXISTS `([^`]+)`/', $statement, $matches)) {
            $currentTable = $matches[1];
            $skipCurrentTable = in_array($currentTable, $excludedTables);
            
            if (!$skipCurrentTable) {
                $processedStatements[] = $statement;
                echo "   - Akan menghapus dan membuat ulang tabel: {$currentTable}\n";
            } else {
                echo "   - Melewati tabel (dipertahankan): {$currentTable}\n";
            }
            continue;
        }
        
        // Deteksi CREATE TABLE
        if (preg_match('/CREATE TABLE `([^`]+)`/', $statement, $matches)) {
            $currentTable = $matches[1];
            $skipCurrentTable = in_array($currentTable, $excludedTables);
            
            if (!$skipCurrentTable) {
                $processedStatements[] = $statement;
            }
            continue;
        }
        
        // Deteksi LOCK/UNLOCK TABLES
        if (preg_match('/LOCK TABLES `([^`]+)`/', $statement, $matches)) {
            $currentTable = $matches[1];
            $skipCurrentTable = in_array($currentTable, $excludedTables);
            
            if (!$skipCurrentTable) {
                $processedStatements[] = $statement;
            }
            continue;
        }
        
        if (preg_match('/UNLOCK TABLES/', $statement)) {
            if (!$skipCurrentTable) {
                $processedStatements[] = $statement;
            }
            continue;
        }
        
        // Deteksi INSERT statements
        if (preg_match('/INSERT INTO `([^`]+)`/', $statement, $matches)) {
            $tableName = $matches[1];
            $skipCurrentTable = in_array($tableName, $excludedTables);
            
            if (!$skipCurrentTable) {
                $processedStatements[] = $statement;
            }
            continue;
        }
        
        // Deteksi ALTER TABLE statements
        if (preg_match('/ALTER TABLE `([^`]+)`/', $statement, $matches)) {
            $tableName = $matches[1];
            $skipCurrentTable = in_array($tableName, $excludedTables);
            
            if (!$skipCurrentTable) {
                $processedStatements[] = $statement;
            }
            continue;
        }
        
        // Statement lainnya (jika tidak sedang skip table)
        if (!$skipCurrentTable && !empty($statement)) {
            $processedStatements[] = $statement;
        }
    }
    
    echo "\n3. Total statements yang akan dijalankan: " . count($processedStatements) . "\n";
    echo "4. Mulai mengeksekusi statements...\n";
    
    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($processedStatements as $index => $statement) {
        try {
            if (!empty(trim($statement))) {
                DB::unprepared($statement);
                $successCount++;
                
                // Progress indicator
                if (($index + 1) % 50 == 0) {
                    echo "   - Diproses: " . ($index + 1) . " statements\n";
                }
            }
        } catch (Exception $e) {
            $errorCount++;
            echo "   - ERROR pada statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "   - Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\n=== HASIL IMPORT ===\n";
    echo "✅ Berhasil: {$successCount} statements\n";
    echo "❌ Error: {$errorCount} statements\n";
    
    echo "\n5. Verifikasi tabel yang dipertahankan:\n";
    
    // Verifikasi bahwa tabel yang dipertahankan masih ada dengan data
    foreach (['users', 'permissions'] as $table) {
        try {
            $count = DB::table($table)->count();
            echo "   - Tabel {$table}: {$count} records (DIPERTAHANKAN)\n";
        } catch (Exception $e) {
            echo "   - Tabel {$table}: ERROR - {$e->getMessage()}\n";
        }
    }
    
    echo "\n6. Contoh tabel yang diimpor:\n";
    
    // Tampilkan beberapa tabel yang berhasil diimpor
    $sampleTables = ['akun_coa', 'master_kapals', 'master_pelabuhans'];
    foreach ($sampleTables as $table) {
        try {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                echo "   - Tabel {$table}: {$count} records (DIIMPOR)\n";
            }
        } catch (Exception $e) {
            echo "   - Tabel {$table}: ERROR - {$e->getMessage()}\n";
        }
    }
    
    echo "\n🎉 IMPORT SELESAI!\n";
    echo "Data berhasil diimpor dari aypsis.sql dengan mempertahankan users dan permissions yang sudah ada.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}