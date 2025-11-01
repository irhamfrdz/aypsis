<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Starting complete data import (excluding users and permissions)...\n\n";

// Path to SQL file
$sqlFile = 'c:\\folder_kerjaan\\aypsis.sql';

if (!file_exists($sqlFile)) {
    echo "❌ SQL file not found: {$sqlFile}\n";
    exit(1);
}

echo "📂 Reading SQL file: {$sqlFile}\n";

// Read the SQL file
$sqlContent = file_get_contents($sqlFile);

if ($sqlContent === false) {
    echo "❌ Failed to read SQL file\n";
    exit(1);
}

echo "✅ SQL file loaded (" . number_format(strlen($sqlContent)) . " bytes)\n\n";

// Split into individual statements
$statements = explode(';', $sqlContent);

// Track statistics
$totalStatements = 0;
$executedStatements = 0;
$skippedStatements = 0;
$errorStatements = 0;

// Tables to exclude (users and permissions)
$excludePatterns = [
    'INSERT INTO `users`',
    'INSERT INTO `permissions`', 
    'INSERT INTO `user_permissions`',
    'INSERT INTO users',
    'INSERT INTO permissions',
    'INSERT INTO user_permissions',
    'CREATE TABLE `users`',
    'CREATE TABLE `permissions`',
    'CREATE TABLE `user_permissions`',
    'CREATE TABLE users',
    'CREATE TABLE permissions', 
    'CREATE TABLE user_permissions'
];

echo "🔄 Processing SQL statements...\n";
echo "⚠️  Excluding: users, permissions, user_permissions tables\n\n";

foreach ($statements as $index => $statement) {
    $statement = trim($statement);
    
    // Skip empty statements
    if (empty($statement)) {
        continue;
    }
    
    $totalStatements++;
    
    // Check if statement should be excluded
    $shouldExclude = false;
    foreach ($excludePatterns as $pattern) {
        if (stripos($statement, $pattern) !== false) {
            $shouldExclude = true;
            break;
        }
    }
    
    if ($shouldExclude) {
        $skippedStatements++;
        if ($totalStatements % 100 == 0) {
            echo "⏭️  Skipped user/permission statement #{$totalStatements}\n";
        }
        continue;
    }
    
    try {
        // Execute the statement
        DB::unprepared($statement);
        $executedStatements++;
        
        // Show progress every 100 statements
        if ($executedStatements % 100 == 0) {
            echo "✅ Executed {$executedStatements} statements...\n";
        }
        
    } catch (Exception $e) {
        $errorStatements++;
        
        // Only show first 10 errors to avoid spam
        if ($errorStatements <= 10) {
            echo "❌ Error in statement #{$totalStatements}: " . substr($e->getMessage(), 0, 100) . "...\n";
        } elseif ($errorStatements == 11) {
            echo "⚠️  More errors encountered, suppressing further error messages...\n";
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 IMPORT RESULTS:\n";
echo "   • Total statements processed: {$totalStatements}\n";
echo "   • Successfully executed: {$executedStatements}\n";
echo "   • Skipped (users/permissions): {$skippedStatements}\n";
echo "   • Errors encountered: {$errorStatements}\n";
echo "   • Success rate: " . round(($executedStatements / max(1, $totalStatements - $skippedStatements)) * 100, 1) . "%\n";

echo "\n🔍 Checking results...\n";

// Recheck database status
$excludeTables = ['users', 'permissions', 'user_permissions', 'migrations'];
$tables = DB::select("SHOW TABLES");
$databaseName = DB::connection()->getDatabaseName();
$tableColumn = "Tables_in_" . $databaseName;

$tablesWithData = 0;
$emptyTables = 0;
$totalRecords = 0;

foreach ($tables as $table) {
    $tableName = $table->$tableColumn;
    
    if (in_array($tableName, $excludeTables)) {
        continue;
    }
    
    $count = DB::table($tableName)->count();
    $totalRecords += $count;
    
    if ($count > 0) {
        $tablesWithData++;
    } else {
        $emptyTables++;
    }
}

echo "📈 Updated Database Status:\n";
echo "   • Tables with data: {$tablesWithData}\n";
echo "   • Empty tables: {$emptyTables}\n";
echo "   • Total records: " . number_format($totalRecords) . "\n";

if ($emptyTables == 0) {
    echo "\n🎉 SUCCESS! All data has been imported!\n";
} else {
    echo "\n⚠️  Some tables may still be empty due to constraints or data dependencies\n";
}

echo "\n✅ Import process completed!\n";