<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\DaftarTagihanKontainerSewaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING ACTUAL IMPORT PROCESS ===\n\n";

// Login as admin (user ID 1)
Auth::loginUsingId(1);
echo "Logged in as: " . Auth::user()->name . "\n\n";

// Use the CSV file from Downloads
$csvPath = 'C:\\Users\\amanda\\Downloads\\export_tagihan_kontainer_sewa_2025-10-02_153813.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: File not found: {$csvPath}\n";
    exit(1);
}

echo "CSV File: {$csvPath}\n";
echo "File size: " . filesize($csvPath) . " bytes\n\n";

// Read first few lines to check format
echo "First 3 lines of CSV:\n";
$handle = fopen($csvPath, 'r');
for ($i = 0; $i < 3 && ($line = fgets($handle)) !== false; $i++) {
    echo "  Line " . ($i + 1) . ": " . substr($line, 0, 100) . "...\n";
}
fclose($handle);
echo "\n";

// Create a fake UploadedFile
$uploadedFile = new UploadedFile(
    $csvPath,
    'export_tagihan_kontainer_sewa_2025-10-02_153813.csv',
    'text/csv',
    null,
    true // test mode
);

// Test 1: WITH validate_only = true (should NOT save)
echo "=== TEST 1: WITH validate_only = TRUE (should NOT save) ===\n";
$controller = new DaftarTagihanKontainerSewaController();

$request = Request::create('/import/process', 'POST', [
    'validate_only' => '1',  // CHECKBOX CHECKED
    'skip_duplicates' => '1',
    'update_existing' => '0',
]);
$request->headers->set('Accept', 'application/json'); // Make it expect JSON
$request->files->set('import_file', $uploadedFile);

$countBefore = DaftarTagihanKontainerSewa::count();
echo "Records before: {$countBefore}\n";

try {
    $response = $controller->processImport($request);
    $data = $response->getData(true);

    echo "Response:\n";
    echo "  Success: " . ($data['success'] ? 'YES' : 'NO') . "\n";
    echo "  Validate Only: " . ($data['validate_only'] ? 'YES' : 'NO') . "\n";
    echo "  Imported: " . ($data['imported_count'] ?? 0) . "\n";
    echo "  Updated: " . ($data['updated_count'] ?? 0) . "\n";
    echo "  Skipped: " . ($data['skipped_count'] ?? 0) . "\n";
    echo "  Errors: " . count($data['errors'] ?? []) . "\n";

    if (!empty($data['errors'])) {
        echo "\n  First 3 errors:\n";
        foreach (array_slice($data['errors'], 0, 3) as $error) {
            echo "    Row {$error['row']}: {$error['message']}\n";
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

$countAfter = DaftarTagihanKontainerSewa::count();
echo "Records after: {$countAfter}\n";
echo "Change: " . ($countAfter - $countBefore) . " records\n";
echo "Expected: 0 (validate only)\n\n";

// Test 2: WITHOUT validate_only = false (SHOULD save)
echo "=== TEST 2: WITHOUT validate_only = FALSE (SHOULD save) ===\n";

$request2 = Request::create('/import/process', 'POST', [
    'validate_only' => '0',  // CHECKBOX NOT CHECKED
    'skip_duplicates' => '1',
    'update_existing' => '0',
]);
$request2->headers->set('Accept', 'application/json'); // Make it expect JSON
$request2->files->set('import_file', $uploadedFile);

$countBefore2 = DaftarTagihanKontainerSewa::count();
echo "Records before: {$countBefore2}\n";

try {
    $response2 = $controller->processImport($request2);
    $data2 = $response2->getData(true);

    echo "Response:\n";
    echo "  Success: " . ($data2['success'] ? 'YES' : 'NO') . "\n";
    echo "  Validate Only: " . ($data2['validate_only'] ? 'YES' : 'NO') . "\n";
    echo "  Imported: " . ($data2['imported_count'] ?? 0) . "\n";
    echo "  Updated: " . ($data2['updated_count'] ?? 0) . "\n";
    echo "  Skipped: " . ($data2['skipped_count'] ?? 0) . "\n";
    echo "  Errors: " . count($data2['errors'] ?? []) . "\n";

    if (!empty($data2['errors'])) {
        echo "\n  First 3 errors:\n";
        foreach (array_slice($data2['errors'], 0, 3) as $error) {
            echo "    Row {$error['row']}: {$error['message']}\n";
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

$countAfter2 = DaftarTagihanKontainerSewa::count();
echo "Records after: {$countAfter2}\n";
echo "Change: " . ($countAfter2 - $countBefore2) . " records\n";
echo "Expected: " . ($data2['imported_count'] ?? 0) . " records\n\n";

echo "=== END TEST ===\n";
