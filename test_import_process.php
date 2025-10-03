<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\DaftarTagihanKontainerSewaController;
use App\Models\DaftarTagihanKontainerSewa;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing CSV Import Process ===\n";

$csvFile = 'C:\\Users\\amanda\\Downloads\\Tagihan Kontainer Sewa DPE.csv';

if (!file_exists($csvFile)) {
    echo "CSV file not found!\n";
    exit(1);
}

echo "Processing file: " . basename($csvFile) . "\n";
echo "File size: " . filesize($csvFile) . " bytes\n";

// Simulate the controller's processCsvImport method
$controller = new DaftarTagihanKontainerSewaController();

try {
    $options = [
        'validate_only' => false,
        'skip_duplicates' => true,
        'update_existing' => false,
    ];

    echo "\nStarting CSV processing...\n";

    $handle = fopen($csvFile, 'r');
    if (!$handle) {
        throw new \Exception('Tidak dapat membaca file CSV');
    }

    $headers = [];
    $rowNumber = 0;
    $results = [
        'success' => true,
        'imported_count' => 0,
        'updated_count' => 0,
        'skipped_count' => 0,
        'errors' => [],
        'warnings' => [],
        'validate_only' => $options['validate_only'],
    ];

    // Detect CSV delimiter
    $firstLine = fgets($handle);
    rewind($handle);
    $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
    echo "Detected delimiter: '" . $delimiter . "'\n";

    while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
        $rowNumber++;

        try {
            // First row is header
            if ($rowNumber === 1) {
                $headers = array_map('trim', $row);
                echo "Headers found: " . implode(', ', array_slice($headers, 0, 10)) . "...\n";
                continue;
            }

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Map row data to associative array
            $data = [];
            foreach ($headers as $index => $header) {
                $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
            }

            echo "\nProcessing row {$rowNumber}:\n";
            echo "  Available keys: " . json_encode(array_keys($data)) . "\n";
            echo "  Container: " . ($data['Kontainer'] ?? 'N/A') . "\n";
            echo "  Group: " . ($data['Group'] ?? 'N/A') . "\n";
            echo "  Date: " . ($data['Awal'] ?? 'N/A') . " - " . ($data['Akhir'] ?? 'N/A') . "\n";
            echo "  DPE format check: " . (in_array('Group', $headers) && in_array('Kontainer', $headers) ? 'YES' : 'NO') . "\n";

            // Clean and validate data using private method access via reflection
            $reflector = new ReflectionClass($controller);
            $cleanMethod = $reflector->getMethod('cleanImportData');
            $cleanMethod->setAccessible(true);

            $cleanedData = $cleanMethod->invokeArgs($controller, [$data, $rowNumber, $headers]);

            echo "  Cleaned data - Container: " . $cleanedData['nomor_kontainer'] . "\n";
            echo "  Cleaned data - Vendor: " . $cleanedData['vendor'] . "\n";
            echo "  Cleaned data - Periode: " . $cleanedData['periode'] . "\n";

            // Check for duplicates
            $findExistingMethod = $reflector->getMethod('findExistingRecord');
            $findExistingMethod->setAccessible(true);
            $existing = $findExistingMethod->invokeArgs($controller, [$cleanedData]);

            if ($existing) {
                if ($options['skip_duplicates'] && !$options['update_existing']) {
                    $results['skipped_count']++;
                    $results['warnings'][] = "Baris {$rowNumber}: Data sudah ada (Kontainer: {$cleanedData['nomor_kontainer']}, Periode: {$cleanedData['periode']}) - diskip";
                    echo "  Status: SKIPPED (duplicate)\n";
                    continue;
                } elseif ($options['update_existing']) {
                    if (!$options['validate_only']) {
                        $existing->update($cleanedData);
                    }
                    $results['updated_count']++;
                    echo "  Status: UPDATED\n";
                    continue;
                }
            }

            // If validation only, don't save
            if (!$options['validate_only']) {
                $record = DaftarTagihanKontainerSewa::create($cleanedData);
                echo "  Status: CREATED with ID " . $record->id . "\n";
            } else {
                echo "  Status: VALIDATED ONLY\n";
            }

            $results['imported_count']++;

            // Limit processing for testing
            if ($rowNumber >= 3) {
                echo "\nStopping at row 3 for testing...\n";
                break;
            }

        } catch (\Exception $e) {
            $results['errors'][] = [
                'row' => $rowNumber,
                'message' => $e->getMessage(),
                'data' => $row
            ];
            $results['success'] = false;
            echo "  Status: ERROR - " . $e->getMessage() . "\n";
        }
    }

    fclose($handle);

    $results['total_processed'] = $results['imported_count'] + $results['updated_count'] + $results['skipped_count'];

    echo "\n=== RESULTS ===\n";
    echo "Success: " . ($results['success'] ? 'YES' : 'NO') . "\n";
    echo "Imported: " . $results['imported_count'] . "\n";
    echo "Updated: " . $results['updated_count'] . "\n";
    echo "Skipped: " . $results['skipped_count'] . "\n";
    echo "Errors: " . count($results['errors']) . "\n";

    if (!empty($results['errors'])) {
        echo "\nErrors:\n";
        foreach ($results['errors'] as $error) {
            echo "  Row " . $error['row'] . ": " . $error['message'] . "\n";
        }
    }

    if (!empty($results['warnings'])) {
        echo "\nWarnings:\n";
        foreach ($results['warnings'] as $warning) {
            echo "  " . $warning . "\n";
        }
    }

} catch (\Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Import Test Complete ===\n";
