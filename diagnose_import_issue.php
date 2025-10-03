<?php

// Diagnostic script to check why import data is not being saved

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== DIAGNOSTIC: Import Issue Investigation ===\n\n";

// 1. Check if table exists
echo "1. Checking if table exists...\n";
$tableExists = Schema::hasTable('daftar_tagihan_kontainer_sewa');
echo "   Table exists: " . ($tableExists ? "YES" : "NO") . "\n\n";

if (!$tableExists) {
    echo "   ERROR: Table does not exist! Please run migrations.\n";
    exit(1);
}

// 2. Check table columns
echo "2. Checking table structure...\n";
$columns = Schema::getColumnListing('daftar_tagihan_kontainer_sewa');
echo "   Columns: " . implode(', ', $columns) . "\n\n";

// 3. Check if Model is configured correctly
echo "3. Checking Model configuration...\n";
try {
    $model = new DaftarTagihanKontainerSewa();
    echo "   Model table: " . $model->getTable() . "\n";
    echo "   Fillable fields: " . implode(', ', $model->getFillable()) . "\n\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 4. Check current data count
echo "4. Checking current data count...\n";
try {
    $count = DaftarTagihanKontainerSewa::count();
    echo "   Current records: {$count}\n\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n\n";
}

// 5. Try to insert test data
echo "5. Testing insert operation...\n";
try {
    DB::beginTransaction();

    $testData = [
        'vendor' => 'TEST_VENDOR',
        'nomor_kontainer' => 'TEST123456',
        'size' => 20,
        'tanggal_awal' => '2025-01-01',
        'tanggal_akhir' => '2025-01-31',
        'periode' => 31,
        'masa' => '31 Hari',
        'tarif' => 25000,
        'hari' => 31,
        'dpp' => 775000,
        'dpp_nilai_lain' => 712916.67,
        'adjustment' => 0,
        'ppn' => 85250,
        'pph' => 0,
        'grand_total' => 860250,
        'status' => 'ongoing',
        'group' => null,
        'status_pranota' => null,
        'pranota_id' => null,
    ];

    echo "   Attempting to create test record...\n";
    $record = DaftarTagihanKontainerSewa::create($testData);
    echo "   SUCCESS! Record ID: " . $record->id . "\n";

    // Verify it was saved
    $verify = DaftarTagihanKontainerSewa::find($record->id);
    echo "   Verification: " . ($verify ? "Record found in database" : "Record NOT found") . "\n";

    // Rollback to keep database clean
    DB::rollBack();
    echo "   (Transaction rolled back for testing)\n\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "   ERROR: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

// 6. Check if there are any database constraints
echo "6. Checking database constraints...\n";
try {
    $indexes = DB::select("SHOW INDEX FROM daftar_tagihan_kontainer_sewa");
    echo "   Indexes found: " . count($indexes) . "\n";
    foreach ($indexes as $index) {
        echo "   - {$index->Key_name} on {$index->Column_name}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   Could not retrieve index info: " . $e->getMessage() . "\n\n";
}

// 7. Simulate the import process with a small CSV
echo "7. Simulating import process...\n";
$csvContent = "vendor;nomor_kontainer;size;group;tanggal_awal;tanggal_akhir;periode;tarif;status\n";
$csvContent .= "DPE;TESTCONT001;20;;2025-01-21;2025-02-20;1;Bulanan;Tersedia\n";

$tempFile = tempnam(sys_get_temp_dir(), 'csv');
file_put_contents($tempFile, $csvContent);

echo "   Created temp CSV file: {$tempFile}\n";
echo "   CSV content:\n";
echo "   " . str_replace("\n", "\n   ", $csvContent) . "\n";

try {
    $handle = fopen($tempFile, 'r');
    $headers = [];
    $rowNumber = 0;
    $delimiter = ';';

    while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
        $rowNumber++;

        if ($rowNumber === 1) {
            $headers = array_map('trim', $row);
            echo "   Headers detected: " . implode(', ', $headers) . "\n";
            continue;
        }

        echo "   Processing row {$rowNumber}...\n";
        echo "   Row data: " . implode(', ', $row) . "\n";

        // Map data
        $data = [];
        foreach ($headers as $index => $header) {
            $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
        }

        echo "   Mapped data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }

    fclose($handle);
    unlink($tempFile);

} catch (Exception $e) {
    echo "   ERROR during CSV simulation: " . $e->getMessage() . "\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "\nPossible issues to check:\n";
echo "1. Is 'validate_only' checkbox checked in the form?\n";
echo "2. Are there any validation errors preventing save?\n";
echo "3. Is there a database transaction that's being rolled back?\n";
echo "4. Check browser console for JavaScript errors\n";
echo "5. Check Laravel log file: storage/logs/laravel.log\n";
