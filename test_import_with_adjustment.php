<?php
/**
 * Test import minimal dengan record yang ada adjustment
 */

echo "=== TEST IMPORT RECORD DENGAN ADJUSTMENT ===\n\n";

// Include Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\DaftarTagihanKontainerSewaController;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

// Create test CSV with adjustment data
$testCsvContent = "Vendor,Nomor Kontainer,Size,Tanggal Awal,Tanggal Akhir,Tarif,Adjustment,Periode,Group,Status\n";
$testCsvContent .= "ZONA,TEST_ADJ_001,20,2024-01-01,2024-01-31,Harian,-5000,1,TEST,ongoing\n";
$testCsvContent .= "ZONA,TEST_ADJ_002,40,2024-01-01,2024-01-31,Bulanan,-10000,1,TEST,ongoing\n";

$testFile = 'test_adjustment_import.csv';
file_put_contents($testFile, $testCsvContent);

echo "Created test CSV:\n";
echo $testCsvContent . "\n";

try {
    // Create controller instance
    $controller = new DaftarTagihanKontainerSewaController();

    // Create mock request
    $request = new Request();
    $request->merge([
        'skip_duplicates' => true,
        'update_existing' => false,
        'validate_only' => false
    ]);

    // Create uploaded file instance
    $uploadedFile = new UploadedFile(
        $testFile,
        'test_adjustment_import.csv',
        'text/csv',
        null,
        true // test mode
    );

    $request->files->set('import_file', $uploadedFile);

    echo "Processing import...\n";

    // Call processImport method using reflection to access private method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('processCsvImport');
    $method->setAccessible(true);

    $options = [
        'skip_duplicates' => true,
        'update_existing' => false,
        'validate_only' => false
    ];

    $result = $method->invoke($controller, $uploadedFile, $options);

    echo "Import result:\n";
    echo "  Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
    echo "  Imported: " . $result['imported_count'] . "\n";
    echo "  Errors: " . count($result['errors']) . "\n";

    if (!empty($result['errors'])) {
        echo "  Error details:\n";
        foreach ($result['errors'] as $error) {
            echo "    Row {$error['row']}: {$error['message']}\n";
        }
    }

    // Check created records
    echo "\nChecking created records:\n";
    $createdRecords = \App\Models\DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', 'TEST_ADJ_%')
        ->orderBy('created_at', 'desc')
        ->get();

    foreach ($createdRecords as $record) {
        echo "  Container: {$record->nomor_kontainer}, Adjustment: {$record->adjustment}, DPP: {$record->dpp}\n";
    }

    // Cleanup
    $createdRecords->each(function($record) {
        $record->delete();
    });
    echo "\nTest records deleted\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

// Cleanup file
unlink($testFile);
echo "Test CSV deleted\n";

// Check logs
echo "\n=== CHECKING LOGS ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -20); // Last 20 lines

    foreach ($recentLines as $line) {
        if (strpos($line, 'adjustment') !== false) {
            echo $line . "\n";
        }
    }
} else {
    echo "Log file not found at: $logFile\n";
}
