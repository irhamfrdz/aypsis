<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Importing filtered SQL file in batches...\n\n";

$filteredSqlFile = 'c:\\folder_kerjaan\\aypsis\\aypsis\\aypsis_filtered.sql';

if (!file_exists($filteredSqlFile)) {
    echo "❌ Filtered SQL file not found: {$filteredSqlFile}\n";
    exit(1);
}

echo "📂 Reading filtered SQL file...\n";
$content = file_get_contents($filteredSqlFile);

if ($content === false) {
    echo "❌ Failed to read filtered SQL file\n";
    exit(1);
}

echo "✅ File loaded (" . number_format(strlen($content)) . " bytes)\n";

// Disable foreign key checks temporarily
echo "🔧 Disabling foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Split by statements - more careful approach
$statements = [];
$currentStatement = '';
$lines = explode("\n", $content);

foreach ($lines as $line) {
    $line = trim($line);
    
    // Skip comments and empty lines
    if (empty($line) || substr($line, 0, 2) == '--' || substr($line, 0, 2) == '/*') {
        continue;
    }
    
    $currentStatement .= $line . ' ';
    
    // If line ends with semicolon, it's end of statement
    if (substr($line, -1) == ';') {
        $statements[] = trim($currentStatement);
        $currentStatement = '';
    }
}

echo "📊 Found " . count($statements) . " SQL statements\n\n";

$executed = 0;
$errors = 0;
$batch = 0;

foreach ($statements as $index => $statement) {
    if (empty($statement)) continue;
    
    try {
        DB::unprepared($statement);
        $executed++;
        
        // Show progress every 50 statements
        if ($executed % 50 == 0) {
            echo "✅ Executed {$executed} statements...\n";
        }
        
    } catch (Exception $e) {
        $errors++;
        
        // Show first 5 errors for debugging
        if ($errors <= 5) {
            echo "❌ Error #{$errors}: " . substr($e->getMessage(), 0, 100) . "...\n";
            echo "   Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
}

// Re-enable foreign key checks
echo "\n🔧 Re-enabling foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 IMPORT RESULTS:\n";
echo "   • Total statements: " . count($statements) . "\n";
echo "   • Successfully executed: {$executed}\n";
echo "   • Errors: {$errors}\n";
echo "   • Success rate: " . round(($executed / count($statements)) * 100, 1) . "%\n";

// Check final database status
echo "\n🔍 Final database check...\n";
$tables = DB::select("SHOW TABLES");
$databaseName = DB::connection()->getDatabaseName();
$tableColumn = "Tables_in_" . $databaseName;

$excludeTables = ['users', 'permissions', 'user_permissions', 'migrations'];
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

echo "📈 Final Database Status:\n";
echo "   • Tables with data: {$tablesWithData}\n";
echo "   • Empty tables: {$emptyTables}\n";
echo "   • Total records: " . number_format($totalRecords) . "\n";

$totalNonExcluded = $tablesWithData + $emptyTables;
$coverage = $totalNonExcluded > 0 ? round(($tablesWithData / $totalNonExcluded) * 100, 1) : 0;
echo "   • Data coverage: {$coverage}%\n";

if ($emptyTables <= 10) {
    echo "\n🎉 EXCELLENT! Almost all data has been imported!\n";
} elseif ($emptyTables <= 20) {
    echo "\n✅ GOOD! Most data has been imported successfully!\n";
} else {
    echo "\n⚠️  Some tables still empty, but significant progress made!\n";
}

echo "\n✅ Import process completed!\n";