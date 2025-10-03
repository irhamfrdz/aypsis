<?php

// Test import dengan file CSV asli Anda

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\DaftarTagihanKontainerSewaController;
use Illuminate\Http\Request;

echo "=== Testing REAL Import with Your CSV ===\n\n";

// Path ke file CSV Anda
$csvPath = 'C:\\Users\\amanda\\Downloads\\template_import_dpe_auto_group.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: CSV file not found: {$csvPath}\n";
    exit(1);
}

echo "CSV file: {$csvPath}\n";
echo "File size: " . filesize($csvPath) . " bytes\n\n";

try {
    // Read first few lines
    $handle = fopen($csvPath, 'r');
    $headers = fgetcsv($handle, 1000, ';');
    echo "Headers: " . implode(', ', $headers) . "\n\n";

    echo "First 5 data rows:\n";
    echo str_repeat("-", 100) . "\n";
    $count = 0;
    while ($count < 5 && ($row = fgetcsv($handle, 1000, ';')) !== false) {
        echo ($count + 1) . ". " . implode(' | ', $row) . "\n";
        $count++;
    }
    fclose($handle);

    echo str_repeat("-", 100) . "\n\n";

    // Now test actual import (dry run)
    echo "Running import test (validate only mode)...\n\n";

    $controller = new DaftarTagihanKontainerSewaController();

    // Create a mock uploaded file
    $uploadedFile = new UploadedFile(
        $csvPath,
        'template_import_dpe_auto_group.csv',
        'text/csv',
        null,
        true // test mode
    );

    // Create mock request
    $request = Request::create('/test', 'POST', [
        'validate_only' => true,
        'skip_duplicates' => true,
        'update_existing' => false,
    ]);

    $request->files->set('import_file', $uploadedFile);

    // Call processImport
    $response = $controller->processImport($request);

    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $result = $response->getData(true);

        echo "Import Result:\n";
        echo str_repeat("=", 80) . "\n";
        echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
        echo "Imported: " . ($result['imported_count'] ?? 0) . "\n";
        echo "Updated: " . ($result['updated_count'] ?? 0) . "\n";
        echo "Skipped: " . ($result['skipped_count'] ?? 0) . "\n";
        echo "Total Processed: " . ($result['total_processed'] ?? 0) . "\n";
        echo "Errors: " . count($result['errors'] ?? []) . "\n";
        echo "Warnings: " . count($result['warnings'] ?? []) . "\n";

        if (!empty($result['errors'])) {
            echo "\nErrors:\n";
            foreach (array_slice($result['errors'], 0, 10) as $error) {
                echo "  Row {$error['row']}: {$error['message']}\n";
            }
        }

        if (!empty($result['warnings'])) {
            echo "\nWarnings (first 10):\n";
            foreach (array_slice($result['warnings'], 0, 10) as $warning) {
                echo "  - {$warning}\n";
            }
        }

        echo str_repeat("=", 80) . "\n";

        if ($result['success'] && empty($result['errors'])) {
            echo "\n✓ SUCCESS! Import akan berhasil jika dijalankan tanpa validate_only mode.\n";
            echo "\nUntuk import data sebenarnya ke database:\n";
            echo "1. Upload file CSV melalui web interface\n";
            echo "2. UNCHECK 'Hanya validasi' checkbox\n";
            echo "3. Klik 'Import Data'\n";
        } else {
            echo "\n✗ Ada error yang perlu diperbaiki terlebih dahulu.\n";
        }

    } else {
        echo "Unexpected response type\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
