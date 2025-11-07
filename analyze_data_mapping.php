<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\StockKontainer;

echo "🔧 Fixing Stock Kontainer Data Mapping\n";
echo "======================================\n\n";

// First, let's examine the source data format more carefully
$file = 'aypsis1.sql';
$handle = fopen($file, 'r');

while (($line = fgets($handle)) !== false) {
    if (strpos($line, "INSERT INTO `stock_kontainers` VALUES") !== false) {
        echo "📄 Source INSERT found, extracting sample data...\n\n";
        
        // Extract first few VALUES entries
        preg_match_all('/\(([^)]+)\)/', $line, $matches);
        
        echo "📊 Sample source VALUES (first 3):\n";
        echo "-----------------------------------\n";
        
        for ($i = 0; $i < min(3, count($matches[1])); $i++) {
            $values = $matches[1][$i];
            echo "Entry " . ($i + 1) . ": (" . $values . ")\n";
            
            // Parse the values
            $parsedValues = [];
            $inQuotes = false;
            $currentValue = '';
            $quoteChar = '';
            
            for ($j = 0; $j < strlen($values); $j++) {
                $char = $values[$j];
                
                if (!$inQuotes && ($char === '"' || $char === "'")) {
                    $inQuotes = true;
                    $quoteChar = $char;
                } elseif ($inQuotes && $char === $quoteChar) {
                    $inQuotes = false;
                    $quoteChar = '';
                } elseif (!$inQuotes && $char === ',') {
                    $parsedValues[] = trim($currentValue);
                    $currentValue = '';
                    continue;
                }
                
                $currentValue .= $char;
            }
            $parsedValues[] = trim($currentValue); // Add last value
            
            echo "  Parsed values:\n";
            foreach ($parsedValues as $idx => $val) {
                echo "    [$idx]: $val\n";
            }
            echo "\n";
        }
        break;
    }
}

fclose($handle);

echo "🧩 Current Table Structure vs Source Data Analysis:\n";
echo "===================================================\n";

$columns = DB::select('DESCRIBE stock_kontainers');
echo "Target columns:\n";
foreach ($columns as $idx => $column) {
    echo "  [$idx]: {$column->Field} ({$column->Type})\n";
}

echo "\n💡 CORRECT MAPPING SHOULD BE:\n";
echo "==============================\n";
echo "Based on the source data analysis:\n";
echo "  [0]: id → id ✅\n";
echo "  [1]: '20' → ukuran (not awalan_kontainer) ❌\n";
echo "  [2]: 'Dry Container' → tipe_kontainer ❌\n";
echo "  [3]: 'available' → status ❌\n";
echo "  [4]: NULL → ? \n";
echo "  [5]: NULL → ?\n";
echo "  [6]: '' → ?\n";
echo "  [7]: 'AYPU' → awalan_kontainer ❌\n";
echo "  [8]: '003386' → nomor_seri_kontainer ❌\n";
echo "  [9]: '0' → akhiran_kontainer ❌\n";
echo "  [10]: 'AYPU0033860' → nomor_seri_gabungan ✅\n";
echo "  [11]: NULL → ?\n";
echo "  [12]: created_at ✅\n";
echo "  [13]: updated_at ✅\n";

echo "\n🔄 Let me create a corrected import script...\n";