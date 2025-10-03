<?php

// Test controller methods exist
echo "=== CONTROLLER METHODS CHECK ===\n\n";

$controller = new \App\Http\Controllers\DaftarTagihanKontainerSewaController();

$methods = ['importPage', 'importCsv', 'processImport'];
foreach ($methods as $method) {
    if (method_exists($controller, $method)) {
        echo "✅ Method '{$method}' EXISTS\n";
    } else {
        echo "❌ Method '{$method}' MISSING\n";
    }
}

echo "\n=== ROUTE GENERATION TEST ===\n\n";

try {
    $importPageRoute = route('daftar-tagihan-kontainer-sewa.import');
    echo "✅ Import page route: {$importPageRoute}\n";
} catch (Exception $e) {
    echo "❌ Import page route error: " . $e->getMessage() . "\n";
}

try {
    $processRoute = route('daftar-tagihan-kontainer-sewa.import.process');
    echo "✅ Process import route: {$processRoute}\n";
} catch (Exception $e) {
    echo "❌ Process import route error: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
