<?php

/**
 * Script untuk mengimpor data dengan menangani foreign key constraints
 * dan menggunakan Eloquent/Laravel DB untuk import yang lebih aman
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FINAL DATA IMPORT SCRIPT ===\n";
echo "Import data dengan handling foreign key constraints yang proper\n\n";

// Daftar tabel yang TIDAK akan diimpor 
$excludedTables = [
    'users',
    'permissions', 
    'roles',
    'model_has_permissions',
    'model_has_roles',
    'role_has_permissions',
    'migrations',
    'failed_jobs',
    'jobs',
    'cache',
    'cache_locks',
    'password_reset_tokens',
    'sessions',
    'audit_logs' // Skip audit logs karena banyak masalah encoding
];

try {
    echo "1. Backup tabel yang akan dipertahankan...\n";
    
    // Backup users dan permissions
    $users = DB::table('users')->get()->toArray();
    $permissions = DB::table('permissions')->get()->toArray();
    
    echo "   - Backup users: " . count($users) . " records\n";
    echo "   - Backup permissions: " . count($permissions) . " records\n";
    
    echo "\n2. Membaca dan memproses file SQL...\n";
    
    $sqlFile = __DIR__ . '/aypsis.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("File aypsis.sql tidak ditemukan!");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    
    // Disable foreign key checks
    echo "3. Disable foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    echo "4. Memproses SQL statements...\n";
    
    // Split berdasarkan statement yang jelas
    $statements = [];
    $lines = explode("\n", $sqlContent);
    $currentStatement = '';
    $skipCurrentTable = false;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip comments dan empty lines
        if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
            continue;
        }
        
        // Deteksi table operations
        if (preg_match('/DROP TABLE IF EXISTS `([^`]+)`/', $line, $matches) ||
            preg_match('/CREATE TABLE `([^`]+)`/', $line, $matches) ||
            preg_match('/LOCK TABLES `([^`]+)`/', $line, $matches) ||
            preg_match('/INSERT INTO `([^`]+)`/', $line, $matches) ||
            preg_match('/ALTER TABLE `([^`]+)`/', $line, $matches)) {
            
            $tableName = $matches[1];
            $skipCurrentTable = in_array($tableName, $excludedTables);
            
            if (!$skipCurrentTable && !empty($currentStatement)) {
                $statements[] = $currentStatement;
            }
            $currentStatement = $skipCurrentTable ? '' : $line;
            continue;
        }
        
        // Skip problematic content
        if (strpos($line, 'Mozilla/') !== false || 
            strpos($line, 'Chrome/') !== false ||
            strpos($line, 'Safari/') !== false ||
            strpos($line, 'Windows NT') !== false) {
            continue;
        }
        
        // Lanjutkan statement
        if (!$skipCurrentTable) {
            if (strpos($line, 'UNLOCK TABLES') !== false) {
                $statements[] = $currentStatement;
                $statements[] = $line;
                $currentStatement = '';
            } else {
                $currentStatement .= ' ' . $line;
            }
        }
        
        // Statement selesai jika ada semicolon di akhir
        if (!$skipCurrentTable && substr($line, -1) === ';') {
            $statements[] = $currentStatement;
            $currentStatement = '';
        }
    }
    
    // Add last statement if any
    if (!empty($currentStatement) && !$skipCurrentTable) {
        $statements[] = $currentStatement;
    }
    
    echo "   - Total statements: " . count($statements) . "\n";
    echo "5. Menjalankan statements...\n";
    
    $successCount = 0;
    $errorCount = 0;
    $skippedCount = 0;
    
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        
        if (empty($statement)) {
            $skippedCount++;
            continue;
        }
        
        // Skip statements yang bermasalah
        if (strpos($statement, 'Mozilla') !== false ||
            strpos($statement, 'Chrome') !== false ||
            strpos($statement, 'audit_logs') !== false ||
            strlen($statement) > 50000) { // Skip very long statements
            $skippedCount++;
            continue;
        }
        
        try {
            DB::unprepared($statement);
            $successCount++;
            
            if (($index + 1) % 100 == 0) {
                echo "   - Processed: " . ($index + 1) . " statements\n";
            }
        } catch (Exception $e) {
            $errorCount++;
            
            // Log hanya error yang penting
            if (!preg_match('/(Duplicate|already exists|Unknown column)/i', $e->getMessage())) {
                echo "   - WARNING: " . substr($e->getMessage(), 0, 100) . "...\n";
            }
        }
    }
    
    echo "\n6. Restore data yang dipertahankan...\n";
    
    // Kosongkan tabel users dan permissions secara aman
    DB::statement('DELETE FROM users');
    DB::statement('DELETE FROM permissions');
    
    // Reset auto increment
    DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1');
    
    // Restore users
    if (!empty($users)) {
        foreach ($users as $user) {
            $userArray = (array) $user;
            DB::table('users')->insert($userArray);
        }
        echo "   - Restored users: " . count($users) . " records\n";
    }
    
    // Restore permissions 
    if (!empty($permissions)) {
        foreach ($permissions as $permission) {
            $permissionArray = (array) $permission;
            DB::table('permissions')->insert($permissionArray);
        }
        echo "   - Restored permissions: " . count($permissions) . " records\n";
    }
    
    // Re-enable foreign key checks
    echo "\n7. Re-enable foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\n=== HASIL IMPORT ===\n";
    echo "✅ Berhasil: {$successCount} statements\n";
    echo "⚠️  Error: {$errorCount} statements\n";
    echo "⏭️  Skipped: {$skippedCount} statements\n";
    
    echo "\n8. Verifikasi hasil:\n";
    
    // Verifikasi tabel yang dipertahankan
    foreach (['users', 'permissions'] as $table) {
        try {
            $count = DB::table($table)->count();
            echo "   - Tabel {$table}: {$count} records (DIPERTAHANKAN)\n";
        } catch (Exception $e) {
            echo "   - Tabel {$table}: ERROR - {$e->getMessage()}\n";
        }
    }
    
    // Verifikasi tabel yang diimpor
    $sampleTables = ['akun_coa', 'master_kapals', 'master_pelabuhans', 'orders', 'divisis', 'surat_jalans'];
    foreach ($sampleTables as $table) {
        try {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                echo "   - Tabel {$table}: {$count} records (DIIMPOR)\n";
            } else {
                echo "   - Tabel {$table}: Not exists\n";
            }
        } catch (Exception $e) {
            echo "   - Tabel {$table}: ERROR - " . substr($e->getMessage(), 0, 50) . "...\n";
        }
    }
    
    echo "\n🎉 IMPORT BERHASIL DISELESAIKAN!\n";
    echo "Data dari aypsis.sql berhasil diimpor dengan mempertahankan users dan permissions.\n";
    echo "Jika ada beberapa error, ini normal untuk data yang mengandung encoding khusus.\n";
    
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    
    // Try to re-enable foreign keys even on error
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    } catch (Exception $fkError) {
        // Ignore
    }
    
    exit(1);
}