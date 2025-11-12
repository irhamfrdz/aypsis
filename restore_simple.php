<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "RESTORE DATABASE FROM SQL FILE\n";
echo "========================================\n\n";

// Step 1: Read the SQL file
echo "[1/3] Reading aypsis1.sql...\n";
$sqlFile = __DIR__ . '/aypsis1.sql';

if (!file_exists($sqlFile)) {
    die("ERROR: File aypsis1.sql not found!\n");
}

$sqlContent = file_get_contents($sqlFile);
echo "File size: " . number_format(strlen($sqlContent)) . " bytes\n\n";

// Step 2: Truncate table
echo "[2/3] Truncating daftar_tagihan_kontainer_sewa table...\n";
try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('daftar_tagihan_kontainer_sewa')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "✓ Table truncated successfully\n\n";
} catch (Exception $e) {
    die("ERROR: Failed to truncate table: " . $e->getMessage() . "\n");
}

// Step 3: Extract and execute INSERT statements
echo "[3/3] Restoring data...\n";

// Find all INSERT statements for daftar_tagihan_kontainer_sewa
preg_match_all(
    '/INSERT INTO `daftar_tagihan_kontainer_sewa` VALUES \(.*?\);/s',
    $sqlContent,
    $matches
);

$insertStatements = $matches[0];
$totalStatements = count($insertStatements);

if ($totalStatements === 0) {
    die("ERROR: No INSERT statements found for daftar_tagihan_kontainer_sewa!\n");
}

echo "Found $totalStatements INSERT statements\n";
echo "Executing...\n";

$successCount = 0;
$errorCount = 0;

foreach ($insertStatements as $index => $statement) {
    try {
        DB::statement($statement);
        $successCount++;
        
        // Progress indicator
        if (($index + 1) % 100 === 0 || ($index + 1) === $totalStatements) {
            echo "Progress: " . ($index + 1) . "/$totalStatements\r";
        }
    } catch (Exception $e) {
        $errorCount++;
        echo "\nERROR on statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
        
        // Show first 100 chars of problematic statement
        echo "Statement: " . substr($statement, 0, 100) . "...\n\n";
        
        // Stop if too many errors
        if ($errorCount > 10) {
            die("Too many errors. Stopping restore.\n");
        }
    }
}

echo "\n\n";
echo "========================================\n";
echo "RESTORE COMPLETED!\n";
echo "========================================\n";
echo "Success: $successCount statements\n";
echo "Errors: $errorCount statements\n\n";

// Verify data
echo "=== VERIFICATION ===\n";
$count = DB::table('daftar_tagihan_kontainer_sewa')->count();
echo "Total records in database: $count\n\n";

if ($count > 0) {
    echo "Sample data (first 5 records):\n";
    $samples = DB::table('daftar_tagihan_kontainer_sewa')
        ->select('id', 'nomor_kontainer', 'size', 'masa', 'periode', 'tarif', 'dpp', 'ppn', 'grand_total')
        ->orderBy('id')
        ->limit(5)
        ->get();
    
    foreach ($samples as $row) {
        echo sprintf(
            "ID: %d | Container: %s | Size: %s | Period: %d | Tarif: %s | DPP: %s | PPN: %s | Grand Total: %s\n",
            $row->id,
            $row->nomor_kontainer,
            $row->size,
            $row->periode,
            $row->tarif,
            number_format($row->dpp, 2),
            number_format($row->ppn, 2),
            number_format($row->grand_total, 2)
        );
    }
    
    echo "\n✅ RESTORE SUCCESSFUL!\n";
} else {
    echo "\n⚠️ WARNING: No data found after restore!\n";
}

echo "\n=== SELESAI ===\n";
