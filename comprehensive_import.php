<?php

/**
 * Script untuk import data yang lebih komprehensif
 * Mengecek dan mengimport setiap tabel satu per satu
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== COMPREHENSIVE DATA IMPORT ===\n";
echo "Mengecek dan import setiap tabel secara detail\n\n";

// Backup users dan permissions dulu
$users = DB::table('users')->get()->toArray();
$permissions = DB::table('permissions')->get()->toArray();

// Daftar tabel yang di-skip
$excludedTables = [
    'users', 'permissions', 'roles', 'model_has_permissions', 
    'model_has_roles', 'role_has_permissions', 'migrations',
    'failed_jobs', 'jobs', 'cache', 'cache_locks', 'password_reset_tokens'
];

$sqlFile = __DIR__ . '/aypsis.sql';
$content = file_get_contents($sqlFile);

// Extract semua INSERT statements per tabel
function extractTableData($content, $tableName) {
    $pattern = "/INSERT INTO `{$tableName}` VALUES (.+?);/s";
    if (preg_match($pattern, $content, $matches)) {
        return $matches[1];
    }
    return null;
}

// Daftar tabel yang harus ada data
$importantTables = [
    'akun_coa' => 'Chart of Accounts',
    'master_kapals' => 'Master Kapal',
    'master_pelabuhans' => 'Master Pelabuhan', 
    'divisis' => 'Divisi',
    'cabangs' => 'Cabang',
    'karyawans' => 'Karyawan',
    'orders' => 'Orders',
    'surat_jalans' => 'Surat Jalan',
    'kontainers' => 'Kontainer',
    'coa_transactions' => 'COA Transactions',
    'pembayaran_pranota' => 'Pembayaran Pranota',
    'jurnal_umum' => 'Jurnal Umum',
    'surat_jalan_approvals' => 'Surat Jalan Approvals',
    'tanda_terimas' => 'Tanda Terima',
    'prospek' => 'Prospek',
    'nomor_terakhir' => 'Nomor Terakhir',
    'pranota_surat_jalans' => 'Pranota Surat Jalan',
    'checkpoints' => 'Checkpoints'
];

DB::statement('SET FOREIGN_KEY_CHECKS=0');

$totalImported = 0;
$successfulTables = 0;
$errorTables = 0;

foreach ($importantTables as $table => $description) {
    if (in_array($table, $excludedTables)) {
        echo "⏭️  Skipping {$table} - {$description}\n";
        continue;
    }
    
    echo "🔍 Processing {$table} - {$description}...\n";
    
    try {
        // Cek apakah tabel sudah ada data
        $currentCount = DB::table($table)->count();
        
        // Extract data dari SQL file
        $insertData = extractTableData($content, $table);
        
        if ($insertData) {
            if ($currentCount == 0) {
                // Import data jika tabel kosong
                echo "   📥 Importing data...\n";
                DB::unprepared("INSERT INTO `{$table}` VALUES " . $insertData);
                
                $newCount = DB::table($table)->count();
                echo "   ✅ Success: {$newCount} records imported\n";
                $totalImported += $newCount;
                $successfulTables++;
            } else {
                echo "   ℹ️  Already has data: {$currentCount} records\n";
                $totalImported += $currentCount;
                $successfulTables++;
            }
        } else {
            echo "   ⚠️  No data found in SQL file\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error: " . substr($e->getMessage(), 0, 100) . "...\n";
        $errorTables++;
        
        // Coba truncate dan import ulang jika ada error
        try {
            if (Schema::hasTable($table)) {
                DB::statement("DELETE FROM `{$table}`");
                if ($insertData) {
                    DB::unprepared("INSERT INTO `{$table}` VALUES " . $insertData);
                    $newCount = DB::table($table)->count();
                    echo "   🔄 Retry success: {$newCount} records imported\n";
                    $totalImported += $newCount;
                    $successfulTables++;
                    $errorTables--;
                }
            }
        } catch (Exception $retryError) {
            echo "   💀 Retry failed: " . substr($retryError->getMessage(), 0, 50) . "...\n";
        }
    }
    
    echo "\n";
}

// Restore users dan permissions
echo "🔐 Restoring users and permissions...\n";

try {
    DB::statement('DELETE FROM users');
    DB::statement('DELETE FROM permissions');
    
    // Restore permissions
    if (!empty($permissions)) {
        foreach ($permissions as $permission) {
            $permissionArray = (array) $permission;
            DB::table('permissions')->insert($permissionArray);
        }
        echo "   ✅ Restored permissions: " . count($permissions) . " records\n";
    }
    
    // Restore users  
    if (!empty($users)) {
        foreach ($users as $user) {
            $userArray = (array) $user;
            DB::table('users')->insert($userArray);
        }
        echo "   ✅ Restored users: " . count($users) . " records\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error restoring: " . $e->getMessage() . "\n";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 FINAL SUMMARY:\n";
echo "- Successful tables: {$successfulTables}\n";
echo "- Error tables: {$errorTables}\n";
echo "- Total records imported: {$totalImported}\n";

echo "\n📋 DETAILED VERIFICATION:\n";
foreach ($importantTables as $table => $description) {
    if (in_array($table, $excludedTables)) continue;
    
    try {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            $status = $count > 0 ? "✅" : "⚠️";
            echo sprintf("   %-25s: %5d records %s\n", $table, $count, $status);
        }
    } catch (Exception $e) {
        echo sprintf("   %-25s: ERROR - %s\n", $table, substr($e->getMessage(), 0, 30));
    }
}

// Verifikasi permissions tetap ada
$permCount = DB::table('permissions')->count();
echo sprintf("   %-25s: %5d records ✅ (PRESERVED)\n", 'permissions', $permCount);

echo "\n🎉 COMPREHENSIVE IMPORT COMPLETED!\n";
echo "Jika masih ada tabel yang kosong, kemungkinan memang tidak ada data di file SQL asli.\n";