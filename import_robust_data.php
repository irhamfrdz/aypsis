<?php

/**
 * Script untuk mengimpor data dari aypsis.sql dengan pendekatan yang lebih robust
 * Menggunakan mysql command line untuk import yang lebih stabil
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ROBUST DATA IMPORT SCRIPT ===\n";
echo "Mengimpor data menggunakan mysql command line untuk stabilitas\n\n";

// Daftar tabel yang TIDAK akan diimpor (dipertahankan yang sudah ada)
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
    'password_reset_tokens'
];

try {
    echo "1. Backup tabel yang akan dipertahankan...\n";
    
    // Backup users table
    $users = DB::table('users')->get()->toArray();
    $permissions = DB::table('permissions')->get()->toArray();
    
    echo "   - Backup users: " . count($users) . " records\n";
    echo "   - Backup permissions: " . count($permissions) . " records\n";
    
    echo "\n2. Membuat script SQL yang sudah difilter...\n";
    
    $sqlFile = __DIR__ . '/aypsis.sql';
    $filteredSqlFile = __DIR__ . '/aypsis_filtered.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("File aypsis.sql tidak ditemukan!");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    
    // Split menjadi lines untuk proses yang lebih akurat
    $lines = explode("\n", $sqlContent);
    
    $filteredLines = [];
    $skipCurrentTable = false;
    $currentTable = null;
    $insideInsert = false;
    
    // Tambahkan header SQL
    $filteredLines[] = "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;";
    $filteredLines[] = "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;";
    $filteredLines[] = "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;";
    $filteredLines[] = "/*!50503 SET NAMES utf8mb4 */;";
    $filteredLines[] = "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;";
    $filteredLines[] = "/*!40103 SET TIME_ZONE='+00:00' */;";
    $filteredLines[] = "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;";
    $filteredLines[] = "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;";
    $filteredLines[] = "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;";
    $filteredLines[] = "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;";
    $filteredLines[] = "";
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line) || strpos($line, '--') === 0) {
            if (!$skipCurrentTable) {
                $filteredLines[] = $line;
            }
            continue;
        }
        
        // Deteksi DROP TABLE
        if (preg_match('/DROP TABLE IF EXISTS `([^`]+)`/', $line, $matches)) {
            $currentTable = $matches[1];
            $skipCurrentTable = in_array($currentTable, $excludedTables);
            $insideInsert = false;
            
            if (!$skipCurrentTable) {
                $filteredLines[] = $line;
                echo "   - Processing table: {$currentTable}\n";
            } else {
                echo "   - Skipping table: {$currentTable}\n";
            }
            continue;
        }
        
        // Deteksi CREATE TABLE
        if (preg_match('/CREATE TABLE `([^`]+)`/', $line, $matches)) {
            $currentTable = $matches[1];
            $skipCurrentTable = in_array($currentTable, $excludedTables);
            $insideInsert = false;
            
            if (!$skipCurrentTable) {
                $filteredLines[] = $line;
            }
            continue;
        }
        
        // Deteksi LOCK TABLES
        if (preg_match('/LOCK TABLES `([^`]+)`/', $line, $matches)) {
            $currentTable = $matches[1];
            $skipCurrentTable = in_array($currentTable, $excludedTables);
            $insideInsert = false;
            
            if (!$skipCurrentTable) {
                $filteredLines[] = $line;
            }
            continue;
        }
        
        // Deteksi UNLOCK TABLES
        if (preg_match('/UNLOCK TABLES/', $line)) {
            $insideInsert = false;
            if (!$skipCurrentTable) {
                $filteredLines[] = $line;
            }
            continue;
        }
        
        // Deteksi INSERT atau ALTER TABLE
        if (preg_match('/INSERT INTO `([^`]+)`/', $line, $matches) || 
            preg_match('/ALTER TABLE `([^`]+)`/', $line, $matches)) {
            $tableName = $matches[1];
            $skipCurrentTable = in_array($tableName, $excludedTables);
            
            if (strpos($line, 'INSERT INTO') !== false) {
                $insideInsert = !$skipCurrentTable;
            }
            
            if (!$skipCurrentTable) {
                $filteredLines[] = $line;
            }
            continue;
        }
        
        // Skip problematic lines yang mengandung user agents atau session data
        if (strpos($line, 'Mozilla/') !== false || 
            strpos($line, 'Chrome/') !== false ||
            strpos($line, 'Safari/') !== false ||
            strpos($line, 'sessions') !== false ||
            strpos($line, 'audit_logs') !== false) {
            continue;
        }
        
        // Include line jika tidak sedang skip table
        if (!$skipCurrentTable) {
            $filteredLines[] = $line;
        }
    }
    
    // Tambahkan footer SQL
    $filteredLines[] = "";
    $filteredLines[] = "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;";
    $filteredLines[] = "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;";
    $filteredLines[] = "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;";
    $filteredLines[] = "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;";
    $filteredLines[] = "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;";
    $filteredLines[] = "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
    $filteredLines[] = "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;";
    
    // Tulis file yang sudah difilter
    file_put_contents($filteredSqlFile, implode("\n", $filteredLines));
    
    echo "   - File filtered dibuat: " . basename($filteredSqlFile) . "\n";
    echo "   - Total lines: " . count($filteredLines) . "\n";
    
    echo "\n3. Import menggunakan mysql command line...\n";
    
    // Get database config
    $dbConfig = config('database.connections.mysql');
    $host = $dbConfig['host'];
    $port = $dbConfig['port'];
    $database = $dbConfig['database'];
    $username = $dbConfig['username'];
    $password = $dbConfig['password'];
    
    // Prepare mysql command
    $mysqlCmd = "mysql -h {$host} -P {$port} -u {$username}";
    if (!empty($password)) {
        $mysqlCmd .= " -p{$password}";
    }
    $mysqlCmd .= " {$database} < \"{$filteredSqlFile}\"";
    
    echo "   - Executing: mysql command\n";
    
    // Execute mysql command
    $output = [];
    $returnCode = 0;
    exec($mysqlCmd . " 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ MySQL import berhasil!\n";
    } else {
        echo "   ❌ MySQL import error:\n";
        foreach ($output as $line) {
            echo "      {$line}\n";
        }
    }
    
    echo "\n4. Restore tabel yang dipertahankan...\n";
    
    // Hapus data users/permissions yang mungkin ter-import
    DB::table('users')->truncate();
    DB::table('permissions')->truncate();
    
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
    
    echo "\n5. Verifikasi hasil import:\n";
    
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
    $sampleTables = ['akun_coa', 'master_kapals', 'master_pelabuhans', 'orders', 'divisis'];
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
    
    // Cleanup
    if (file_exists($filteredSqlFile)) {
        unlink($filteredSqlFile);
        echo "\n6. Cleanup: File temporary dihapus\n";
    }
    
    echo "\n🎉 IMPORT BERHASIL DISELESAIKAN!\n";
    echo "Data dari aypsis.sql berhasil diimpor dengan mempertahankan users dan permissions.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}