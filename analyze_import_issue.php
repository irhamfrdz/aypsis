<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Analyzing Stock Kontainer Import Issue\n";
echo "==========================================\n\n";

// Check table structure
echo "📋 Table Structure:\n";
echo "-------------------\n";

$columns = DB::select('DESCRIBE stock_kontainers');

foreach ($columns as $column) {
    echo sprintf("%-25s | %-15s | %s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null === 'YES' ? 'NULL' : 'NOT NULL'
    );
}

echo "\n🔍 Raw Data Sample (First 3 records):\n";
echo "--------------------------------------\n";

$rawData = DB::select('SELECT * FROM stock_kontainers LIMIT 3');

foreach ($rawData as $index => $row) {
    echo "Record " . ($index + 1) . ":\n";
    foreach ($row as $field => $value) {
        echo "  {$field}: '" . (is_null($value) ? 'NULL' : $value) . "'\n";
    }
    echo "\n";
}

echo "🔄 Now let me check the source SQL file structure...\n";
echo "====================================================\n";

// Let's examine what the actual INSERT statement looks like
$file = 'aypsis1.sql';
$handle = fopen($file, 'r');
$lineNumber = 0;

while (($line = fgets($handle)) !== false) {
    $lineNumber++;
    
    if (strpos($line, "INSERT INTO `stock_kontainers` VALUES") !== false) {
        echo "📄 Found INSERT statement at line {$lineNumber}:\n";
        echo "First 200 characters:\n";
        echo substr($line, 0, 200) . "...\n\n";
        
        // Count VALUES entries
        $valuesCount = substr_count($line, '),(');
        echo "📊 Total VALUES entries: " . ($valuesCount + 1) . "\n";
        
        // Show first few VALUES
        preg_match('/VALUES\s*(\([^)]+\))/', $line, $matches);
        if ($matches) {
            echo "📝 First VALUES entry:\n";
            echo $matches[1] . "\n";
        }
        break;
    }
}

fclose($handle);

echo "\n❗ ISSUE DIAGNOSIS:\n";
echo "==================\n";
echo "The data appears to be imported but in wrong columns.\n";
echo "This suggests a mismatch between source and target table structure.\n";
echo "\nNeed to verify column order and data mapping.\n";