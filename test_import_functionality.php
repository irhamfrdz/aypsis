<?php

echo "=== IMPORT FUNCTIONALITY TEST ===\n\n";

// Test if we can access the import page
echo "1. Testing import page access...\n";
try {
    $controller = new \App\Http\Controllers\DaftarTagihanKontainerSewaController();
    $response = $controller->importPage();
    echo "✅ Import page accessible\n";
} catch (Exception $e) {
    echo "❌ Import page error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing CSV file existence...\n";
$csvFile = 'TAGIHAN_DPE_IMPORT_READY.csv';
if (file_exists($csvFile)) {
    $fileSize = round(filesize($csvFile) / 1024, 2);
    $lineCount = count(file($csvFile));
    echo "✅ CSV file found: {$csvFile} ({$fileSize} KB, {$lineCount} lines)\n";

    // Show first few lines
    echo "\nFirst 3 lines of CSV:\n";
    $lines = array_slice(file($csvFile), 0, 3);
    foreach ($lines as $i => $line) {
        echo ($i+1) . ": " . trim($line) . "\n";
    }
} else {
    echo "❌ CSV file not found: {$csvFile}\n";
}

echo "\n3. Testing CSV parsing methods...\n";
try {
    $controller = new \App\Http\Controllers\DaftarTagihanKontainerSewaController();

    // Test if the helper methods exist
    $reflection = new ReflectionClass($controller);

    $methods = ['cleanDpeFormatData', 'parseDpeDate', 'cleanDpeNumber'];
    foreach ($methods as $method) {
        if ($reflection->hasMethod($method)) {
            echo "✅ Method '{$method}' exists\n";
        } else {
            echo "❌ Method '{$method}' missing\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Method check error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing user permissions...\n";
try {
    // Get admin user
    $adminUser = \App\Models\User::where('username', 'admin')->first();
    if ($adminUser) {
        auth()->login($adminUser);

        $hasCreate = $adminUser->can('tagihan-kontainer-sewa-create');
        $hasIndex = $adminUser->can('tagihan-kontainer-sewa-index');

        echo "✅ Admin user logged in\n";
        echo "Create permission: " . ($hasCreate ? '✅' : '❌') . "\n";
        echo "Index permission: " . ($hasIndex ? '✅' : '❌') . "\n";
    } else {
        echo "❌ Admin user not found\n";
    }
} catch (Exception $e) {
    echo "❌ Permission test error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
