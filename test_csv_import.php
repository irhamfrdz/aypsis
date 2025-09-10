<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test import CSV
try {
    echo "Testing CSV Import Functionality\n";
    echo "=================================\n\n";

    $csvFile = __DIR__ . '/test_import_corrected.csv';

    if (!file_exists($csvFile)) {
        echo "Error: CSV file not found!\n";
        exit(1);
    }

    echo "1. Reading CSV file: " . $csvFile . "\n";

    $handle = fopen($csvFile, 'r');
    if (!$handle) {
        echo "Error: Cannot open CSV file!\n";
        exit(1);
    }

    // Read header
    $header = fgetcsv($handle, 0, ';');
    echo "2. CSV Headers found: " . count($header) . " columns\n";
    print_r($header);

    echo "\n3. Reading data rows:\n";
    $rowCount = 0;
    $validRows = 0;

    while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
        $rowCount++;
        echo "Row $rowCount: " . count($data) . " columns\n";

        if (count($data) == count($header)) {
            $validRows++;
            $row = array_combine($header, $data);
            echo "  - NIK: " . $row['nik'] . "\n";
            echo "  - Nama: " . $row['nama_lengkap'] . "\n";
            echo "  - Email: " . $row['email'] . "\n";
            echo "  - Divisi: " . $row['divisi'] . "\n";
            echo "  - Valid row data\n";
        } else {
            echo "  - Column count mismatch! Expected " . count($header) . ", got " . count($data) . "\n";
        }
        echo "\n";
    }

    fclose($handle);

    echo "4. Summary:\n";
    echo "   Total rows: $rowCount\n";
    echo "   Valid rows: $validRows\n";
    echo "   Invalid rows: " . ($rowCount - $validRows) . "\n";

    if ($validRows > 0) {
        echo "\n✅ CSV Import Test: PASSED\n";
        echo "The CSV file is properly formatted and can be imported.\n";
    } else {
        echo "\n❌ CSV Import Test: FAILED\n";
        echo "No valid rows found in CSV file.\n";
    }

} catch (Exception $e) {
    echo "Error during CSV import test: " . $e->getMessage() . "\n";
    exit(1);
}
