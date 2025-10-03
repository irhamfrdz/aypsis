<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== TEST IMPORT FUNCTIONALITY ===\n\n";

// Test if we can access the controller
try {
    $controller = new \App\Http\Controllers\DaftarTagihanKontainerSewaController();

    // Test importPage method
    if (method_exists($controller, 'importPage')) {
        echo "✅ importPage method exists\n";
    } else {
        echo "❌ importPage method missing\n";
    }

    // Test processImport method
    if (method_exists($controller, 'processImport')) {
        echo "✅ processImport method exists\n";
    } else {
        echo "❌ processImport method missing\n";
    }

    // Test exportTemplate method
    if (method_exists($controller, 'exportTemplate')) {
        echo "✅ exportTemplate method exists\n";
    } else {
        echo "❌ exportTemplate method missing\n";
    }

    echo "\n=== ROUTE TESTING ===\n\n";

    // Test route generation
    try {
        $importRoute = route('daftar-tagihan-kontainer-sewa.import');
        echo "✅ Import page route: {$importRoute}\n";
    } catch (Exception $e) {
        echo "❌ Import page route error: " . $e->getMessage() . "\n";
    }

    try {
        $processRoute = route('daftar-tagihan-kontainer-sewa.import.process');
        echo "✅ Process import route: {$processRoute}\n";
    } catch (Exception $e) {
        echo "❌ Process import route error: " . $e->getMessage() . "\n";
    }

    try {
        $templateRoute = route('daftar-tagihan-kontainer-sewa.export-template');
        echo "✅ Template export route: {$templateRoute}\n";
    } catch (Exception $e) {
        echo "❌ Template export route error: " . $e->getMessage() . "\n";
    }

    echo "\n=== FILE VALIDATION TEST ===\n\n";

    // Test CSV file header parsing
    $sampleCsvPath = 'C:\\Users\\amanda\\Downloads\\template_import_dpe_auto_group.csv';
    if (file_exists($sampleCsvPath)) {
        echo "✅ CSV file found: {$sampleCsvPath}\n";

        $file = new \SplFileObject($sampleCsvPath, 'r');
        $header = $file->fgetcsv(';'); // Use semicolon delimiter

        echo "📄 CSV Headers found:\n";
        foreach ($header as $index => $col) {
            echo "  [{$index}] '{$col}'\n";
        }

        // Check for expected columns
        $expectedColumns = ['vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir'];
        $foundColumns = array_intersect($expectedColumns, $header);

        echo "\n📊 Column validation:\n";
        foreach ($expectedColumns as $col) {
            if (in_array($col, $header)) {
                echo "  ✅ {$col} - Found\n";
            } else {
                echo "  ❌ {$col} - Missing\n";
            }
        }

        // Read first data row as sample
        $firstDataRow = $file->fgetcsv(';');
        if ($firstDataRow) {
            echo "\n📋 Sample data row:\n";
            foreach ($header as $index => $colName) {
                $value = $firstDataRow[$index] ?? '';
                echo "  {$colName}: '{$value}'\n";
            }
        }

    } else {
        echo "❌ CSV file not found: {$sampleCsvPath}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
