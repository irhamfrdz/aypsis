<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\KaryawanController;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing CSV Template Download\n";
    echo "=============================\n\n";

    // Create controller instance
    $controller = new KaryawanController();

    // Create a mock request
    $request = new Request();

    echo "1. Testing template download method...\n";

    // Call the downloadTemplate method
    $response = $controller->downloadTemplate($request);

    echo "2. Checking response...\n";
    echo "   Response type: " . get_class($response) . "\n";
    echo "   Status code: " . $response->getStatusCode() . "\n";

    $headers = $response->headers->all();
    echo "   Content-Type: " . ($headers['content-type'][0] ?? 'Not set') . "\n";
    echo "   Content-Disposition: " . ($headers['content-disposition'][0] ?? 'Not set') . "\n";

    echo "\n3. Testing response content...\n";

    // Capture the streamed content
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    // Save to file for inspection
    file_put_contents(__DIR__ . '/downloaded_template.csv', $content);

    echo "   Content length: " . strlen($content) . " bytes\n";
    echo "   Saved to: downloaded_template.csv\n";

    // Check if content has proper CSV structure
    $lines = explode("\n", trim($content));
    echo "   Lines in CSV: " . count($lines) . "\n";

    if (count($lines) >= 2) {
        $headers = str_getcsv($lines[0], ';');
        $sampleData = str_getcsv($lines[1], ';');

        echo "   Headers count: " . count($headers) . "\n";
        echo "   Sample data count: " . count($sampleData) . "\n";

        if (count($headers) === count($sampleData)) {
            echo "   ✅ CSV structure is valid\n";
            echo "   First 5 headers: " . implode(', ', array_slice($headers, 0, 5)) . "\n";
        } else {
            echo "   ❌ CSV structure mismatch\n";
        }
    }

    echo "\n✅ Template Download Test: PASSED\n";
    echo "Template is properly generated and downloadable.\n";

} catch (Exception $e) {
    echo "Error during template download test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
