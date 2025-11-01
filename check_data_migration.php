<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking data migration status (excluding users and permissions)...\n\n";

// Get all tables in current database (excluding users and permissions)
$excludeTables = ['users', 'permissions', 'user_permissions', 'migrations'];

try {
    // Get all table names
    $tables = DB::select("SHOW TABLES");
    $databaseName = DB::connection()->getDatabaseName();
    $tableColumn = "Tables_in_" . $databaseName;
    
    echo "📊 Database Analysis Report\n";
    echo "=" . str_repeat("=", 50) . "\n\n";
    
    $totalTables = 0;
    $tablesWithData = 0;
    $emptyTables = 0;
    $totalRecords = 0;
    
    foreach ($tables as $table) {
        $tableName = $table->$tableColumn;
        
        // Skip excluded tables
        if (in_array($tableName, $excludeTables)) {
            continue;
        }
        
        $totalTables++;
        
        // Get record count for each table
        $count = DB::table($tableName)->count();
        $totalRecords += $count;
        
        if ($count > 0) {
            $tablesWithData++;
            echo "✅ {$tableName}: {$count} records\n";
        } else {
            $emptyTables++;
            echo "⚪ {$tableName}: 0 records (empty)\n";
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📈 SUMMARY:\n";
    echo "   • Total tables (excl. users/permissions): {$totalTables}\n";
    echo "   • Tables with data: {$tablesWithData}\n";
    echo "   • Empty tables: {$emptyTables}\n";
    echo "   • Total records: " . number_format($totalRecords) . "\n";
    echo "   • Data migration coverage: " . round(($tablesWithData / $totalTables) * 100, 1) . "%\n";
    
    if ($emptyTables > 0) {
        echo "\n⚠️  Empty tables found - possible incomplete migration\n";
    } else {
        echo "\n🎉 All tables have data - migration appears complete!\n";
    }
    
    // Check some key business tables specifically
    echo "\n🔍 Key Business Tables Check:\n";
    echo str_repeat("-", 40) . "\n";
    
    $keyTables = [
        'karyawans' => 'Employee data',
        'master_kapal' => 'Ship master data', 
        'master_pelabuhan' => 'Port master data',
        'kontainers' => 'Container data',
        'permohonan' => 'Application/request data',
        'prospek' => 'Prospect data',
        'order_management' => 'Order data',
        'master_vendor_bengkel' => 'Workshop vendor data',
        'jenis_barang' => 'Goods type data',
        'master_tujuan_kegiatan_utama' => 'Main activity purpose data'
    ];
    
    foreach ($keyTables as $tableName => $description) {
        try {
            $count = DB::table($tableName)->count();
            $status = $count > 0 ? "✅" : "❌";
            echo "{$status} {$description}: {$count} records\n";
        } catch (Exception $e) {
            echo "❓ {$description}: Table not found\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error checking database: " . $e->getMessage() . "\n";
}