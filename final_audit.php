<?php

/**
 * Script untuk mengecek tabel mana saja yang ada di SQL file
 * tapi belum ter-import, dan mencoba import tabel yang tersisa
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FINAL DATA AUDIT & IMPORT ===\n";
echo "Mengecek semua tabel dan memastikan tidak ada yang terlewat\n\n";

$sqlFile = __DIR__ . '/aypsis.sql';
$content = file_get_contents($sqlFile);

// Extract semua nama tabel dari SQL file
preg_match_all('/INSERT INTO `([^`]+)` VALUES/', $content, $matches);
$tablesInSql = array_unique($matches[1]);

// Daftar tabel yang di-skip
$excludedTables = [
    'users', 'permissions', 'roles', 'model_has_permissions', 
    'model_has_roles', 'role_has_permissions', 'migrations',
    'failed_jobs', 'jobs', 'cache', 'cache_locks', 'password_reset_tokens',
    'sessions', 'audit_logs' // Skip karena masalah encoding
];

echo "📋 TABLES FOUND IN SQL FILE:\n";
foreach ($tablesInSql as $table) {
    $excluded = in_array($table, $excludedTables) ? " (EXCLUDED)" : "";
    echo "   - {$table}{$excluded}\n";
}

echo "\n🔍 CHECKING & IMPORTING MISSING DATA:\n";

$newImports = 0;
$totalNewRecords = 0;

DB::statement('SET FOREIGN_KEY_CHECKS=0');

foreach ($tablesInSql as $table) {
    if (in_array($table, $excludedTables)) {
        continue;
    }
    
    try {
        if (Schema::hasTable($table)) {
            $currentCount = DB::table($table)->count();
            
            if ($currentCount == 0) {
                // Tabel kosong, coba import
                echo "📥 Importing {$table}...\n";
                
                $pattern = "/INSERT INTO `{$table}` VALUES (.+?);/s";
                if (preg_match($pattern, $content, $matches)) {
                    $insertData = $matches[1];
                    
                    try {
                        DB::unprepared("INSERT INTO `{$table}` VALUES " . $insertData);
                        $newCount = DB::table($table)->count();
                        echo "   ✅ Success: {$newCount} records imported\n";
                        $newImports++;
                        $totalNewRecords += $newCount;
                    } catch (Exception $e) {
                        echo "   ❌ Error: " . substr($e->getMessage(), 0, 80) . "...\n";
                    }
                } else {
                    echo "   ⚠️  No INSERT data found in SQL\n";
                }
            } else {
                echo "✅ {$table}: {$currentCount} records (already exists)\n";
            }
        } else {
            echo "❌ {$table}: Table doesn't exist in schema\n";
        }
    } catch (Exception $e) {
        echo "💀 {$table}: Critical error - " . substr($e->getMessage(), 0, 50) . "...\n";
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 AUDIT SUMMARY:\n";
echo "- Tables found in SQL: " . count($tablesInSql) . "\n";
echo "- Tables excluded: " . count($excludedTables) . "\n";
echo "- New imports this round: {$newImports}\n";
echo "- New records imported: {$totalNewRecords}\n";

echo "\n📋 FINAL DATABASE STATE:\n";

$grandTotal = 0;
$tablesWithData = 0;
$emptyTables = 0;

// Check all tables yang ada di SQL file
foreach ($tablesInSql as $table) {
    if (in_array($table, $excludedTables)) continue;
    
    try {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            $grandTotal += $count;
            
            if ($count > 0) {
                $tablesWithData++;
                echo sprintf("   %-30s: %6d records ✅\n", $table, $count);
            } else {
                $emptyTables++;
                echo sprintf("   %-30s: %6d records ⚠️\n", $table, $count);
            }
        }
    } catch (Exception $e) {
        echo sprintf("   %-30s: ERROR\n", $table);
    }
}

// Check preserved tables
echo sprintf("   %-30s: %6d records 🔐 (PRESERVED)\n", 'permissions', DB::table('permissions')->count());
echo sprintf("   %-30s: %6d records 🔐 (PRESERVED)\n", 'users', DB::table('users')->count());

echo "\n🎯 FINAL STATISTICS:\n";
echo "- Tables with data: {$tablesWithData}\n";
echo "- Empty tables: {$emptyTables}\n";
echo "- Total records in database: {$grandTotal}\n";
echo "- Preserved permissions: " . DB::table('permissions')->count() . "\n";

echo "\n🚀 DATABASE IMPORT STATUS: ";
if ($emptyTables <= 2) {
    echo "EXCELLENT ✨\n";
    echo "Database import sangat sukses! Hanya beberapa tabel kosong yang mungkin memang tidak ada datanya.\n";
} elseif ($emptyTables <= 5) {
    echo "GOOD ✅\n";
    echo "Database import berhasil dengan baik. Beberapa tabel kosong normal untuk data development.\n";
} else {
    echo "NEEDS REVIEW ⚠️\n";
    echo "Masih ada {$emptyTables} tabel kosong. Mungkin perlu dicek manual.\n";
}

echo "\n💡 RECOMMENDATIONS:\n";
echo "1. Jalankan aplikasi: php artisan serve\n";
echo "2. Test login dan navigasi\n";
echo "3. Verifikasi data melalui interface web\n";
echo "4. Backup database current jika sudah puas\n";

echo "\n" . str_repeat("=", 70) . "\n";