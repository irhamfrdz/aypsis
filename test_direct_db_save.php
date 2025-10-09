<?php
/**
 * Test langsung save ke database dengan adjustment
 */

echo "=== TEST DIRECT DATABASE SAVE ===\n\n";

// Include Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "Test 1: Direct model create with adjustment\n";
try {
    $testData = [
        'vendor' => 'TEST',
        'nomor_kontainer' => 'TEST_ADJUSTMENT_001',
        'size' => '20',
        'tanggal_awal' => '2024-01-01',
        'tanggal_akhir' => '2024-01-31',
        'tarif' => 'Harian',
        'adjustment' => -12345.67,
        'periode' => 1,
        'group' => 'TEST',
        'status' => 'ongoing',
        'masa' => '1 Jan 2024 - 31 Jan 2024',
        'dpp' => 100000,
        'ppn' => 11000,
        'pph' => 2000,
        'grand_total' => 109000,
    ];

    echo "Data yang akan disave:\n";
    foreach ($testData as $key => $value) {
        echo "  $key: $value\n";
    }

    $record = DaftarTagihanKontainerSewa::create($testData);
    echo "\n✓ Record berhasil dibuat dengan ID: " . $record->id . "\n";
    echo "Adjustment tersimpan: " . $record->adjustment . "\n\n";

    // Query untuk memverifikasi
    $saved = DaftarTagihanKontainerSewa::find($record->id);
    echo "Verifikasi dari database:\n";
    echo "  ID: " . $saved->id . "\n";
    echo "  Container: " . $saved->nomor_kontainer . "\n";
    echo "  Adjustment: " . $saved->adjustment . "\n";
    echo "  DPP: " . $saved->dpp . "\n";

    // Cleanup
    $saved->delete();
    echo "\nTest record deleted\n";

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test 2: Check recent imports with adjustment\n";

try {
    $recentRecords = DaftarTagihanKontainerSewa::where('created_at', '>=', date('Y-m-d'))
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    echo "Recent records found: " . $recentRecords->count() . "\n\n";

    foreach ($recentRecords as $record) {
        echo "ID: {$record->id} | Container: {$record->nomor_kontainer} | ";
        echo "Adjustment: {$record->adjustment} | DPP: {$record->dpp} | ";
        echo "Created: {$record->created_at}\n";
    }

    $adjustmentRecords = $recentRecords->where('adjustment', '!=', 0);
    echo "\nRecords dengan adjustment bukan 0: " . $adjustmentRecords->count() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test 3: Test CSV file yang punya adjustment\n";

$csvFile = 'Zona_SIAP_IMPORT_FINAL_TARIF_BENAR_COMMA.csv';
if (file_exists($csvFile)) {
    $handle = fopen($csvFile, 'r');
    $headers = fgetcsv($handle, 1000, ',');

    $adjustmentIndex = array_search('Adjustment', $headers);
    if ($adjustmentIndex !== false) {
        echo "Adjustment column found at index: $adjustmentIndex\n";

        $rowCount = 0;
        $nonZeroCount = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false && $rowCount < 20) {
            $rowCount++;
            $adjustmentValue = isset($row[$adjustmentIndex]) ? trim($row[$adjustmentIndex]) : '';

            if (!empty($adjustmentValue) && $adjustmentValue != '0' && $adjustmentValue != '0.00') {
                $nonZeroCount++;
                echo "Row $rowCount: Container {$row[1]}, Adjustment: '$adjustmentValue'\n";
            }
        }

        echo "\nTotal rows checked: $rowCount\n";
        echo "Rows with non-zero adjustment: $nonZeroCount\n";
    } else {
        echo "Adjustment column not found in CSV\n";
    }

    fclose($handle);
} else {
    echo "CSV file not found\n";
}
