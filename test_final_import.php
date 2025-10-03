<?php

require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\DaftarTagihanKontainerSewaController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Auth\User;

echo "=== Final Import Test ===\n";

// Set up user authentication
$user = new User();
$user->id = 1;
$user->name = 'Test User';
auth()->login($user);

// Set up the controller
$controller = new DaftarTagihanKontainerSewaController();

// Prepare file path
$csvFile = __DIR__ . '/Tagihan Kontainer Sewa DPE.csv';
echo "Testing file: " . basename($csvFile) . "\n";
echo "File size: " . filesize($csvFile) . " bytes\n";

// Create a temporary copy to simulate upload
$tempFile = tempnam(sys_get_temp_dir(), 'csv_test');
copy($csvFile, $tempFile);

// Create UploadedFile instance
$uploadedFile = new UploadedFile(
    $tempFile,
    'Tagihan Kontainer Sewa DPE.csv',
    'text/csv',
    null,
    true
);

// Create request object
$request = new Request();
$request->files->set('csv_file', $uploadedFile);
$request->merge([
    'validate_only' => false,
    'skip_duplicates' => true,
    'update_existing' => false
]);

echo "\nProcessing import...\n";

try {
    // Call the actual import method
    $response = $controller->importCsv($request);

    // Check if it's a JSON response
    if (method_exists($response, 'getData')) {
        $data = $response->getData(true);

        echo "=== IMPORT RESULTS ===\n";
        echo "Success: " . ($data['success'] ? 'YES' : 'NO') . "\n";
        echo "Message: " . $data['message'] . "\n";

        if (isset($data['imported_count'])) {
            echo "Imported: " . $data['imported_count'] . "\n";
        }
        if (isset($data['updated_count'])) {
            echo "Updated: " . $data['updated_count'] . "\n";
        }
        if (isset($data['skipped_count'])) {
            echo "Skipped: " . $data['skipped_count'] . "\n";
        }
        if (isset($data['error_count'])) {
            echo "Errors: " . $data['error_count'] . "\n";
        }

        if (!empty($data['errors'])) {
            echo "\nErrors:\n";
            foreach ($data['errors'] as $error) {
                echo "  " . $error . "\n";
            }
        }

        if (!empty($data['warnings'])) {
            echo "\nWarnings:\n";
            foreach ($data['warnings'] as $warning) {
                echo "  " . $warning . "\n";
            }
        }
    } else {
        echo "Response: " . $response->getContent() . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} finally {
    // Clean up
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
}

echo "\n=== Final Import Test Complete ===\n";
