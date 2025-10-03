<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Import Debug ===\n";

// 1. Check if table exists
echo "1. Checking table existence...\n";
try {
    $tableExists = Schema::hasTable('daftar_tagihan_kontainer_sewa');
    echo "   Table 'daftar_tagihan_kontainer_sewa' exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "   Error checking table: " . $e->getMessage() . "\n";
}

// 2. Check table structure
echo "\n2. Checking table structure...\n";
try {
    $columns = Schema::getColumnListing('daftar_tagihan_kontainer_sewa');
    echo "   Columns: " . implode(', ', $columns) . "\n";
} catch (Exception $e) {
    echo "   Error getting columns: " . $e->getMessage() . "\n";
}

// 3. Check data count
echo "\n3. Checking current data count...\n";
try {
    $count = DaftarTagihanKontainerSewa::count();
    echo "   Current records count: " . $count . "\n";
} catch (Exception $e) {
    echo "   Error counting records: " . $e->getMessage() . "\n";
}

// 4. Test CSV file path
echo "\n4. Testing CSV file...\n";
$csvFile = 'C:\\Users\\amanda\\Downloads\\Tagihan Kontainer Sewa DPE.csv';
if (file_exists($csvFile)) {
    echo "   CSV file exists: YES\n";
    echo "   File size: " . filesize($csvFile) . " bytes\n";

    // Read first few lines
    $handle = fopen($csvFile, 'r');
    if ($handle) {
        $lineCount = 0;
        echo "   First 3 lines:\n";
        while (($line = fgets($handle)) !== false && $lineCount < 3) {
            echo "   Line " . ($lineCount + 1) . ": " . trim($line) . "\n";
            $lineCount++;
        }
        fclose($handle);
    }
} else {
    echo "   CSV file exists: NO\n";
}

// 5. Test single record creation
echo "\n5. Testing single record creation...\n";
try {
    $testData = [
        'vendor' => 'DPE',
        'nomor_kontainer' => 'TEST123456',
        'size' => '20',
        'tanggal_awal' => '2025-01-01',
        'tanggal_akhir' => '2025-01-31',
        'periode' => 31,
        'masa' => '31 Hari',
        'tarif' => 750000,
        'status' => 'ongoing',
        'dpp' => 775000,
        'ppn' => 85250,
        'pph' => 15500,
        'grand_total' => 844750,
        'group' => 'TEST'
    ];

    // Check if test record already exists
    $existing = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'TEST123456')->first();
    if ($existing) {
        echo "   Test record already exists, deleting...\n";
        $existing->delete();
    }

    $record = DaftarTagihanKontainerSewa::create($testData);
    echo "   Test record created successfully with ID: " . $record->id . "\n";

    // Clean up
    $record->delete();
    echo "   Test record deleted\n";

} catch (Exception $e) {
    echo "   Error creating test record: " . $e->getMessage() . "\n";
}

// 6. Check Laravel logs
echo "\n6. Checking recent Laravel logs...\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -10);
    echo "   Last 10 log lines:\n";
    foreach ($recentLines as $line) {
        if (!empty(trim($line))) {
            echo "   " . trim($line) . "\n";
        }
    }
} else {
    echo "   Log file not found\n";
}

echo "\n=== Debug Complete ===\n";
