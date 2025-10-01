<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

// Simple test script
echo "Testing admin bank access...\n";

// Check if we can access the route configuration
$routeFile = __DIR__ . '/routes/web.php';
if (!file_exists($routeFile)) {
    echo "Error: routes/web.php not found\n";
    exit(1);
}

// Read the route file content
$routeContent = file_get_contents($routeFile);

// Check if master/bank is inside a middleware group
if (strpos($routeContent, "Route::resource('master/bank'") !== false) {

    // Find the line with master/bank
    $lines = explode("\n", $routeContent);
    $bankLineNumber = 0;
    $middlewareGroupFound = false;

    foreach ($lines as $index => $line) {
        if (strpos($line, "Route::resource('master/bank'") !== false) {
            $bankLineNumber = $index + 1;
            break;
        }
    }

    echo "Found master/bank route at line: $bankLineNumber\n";

    // Check lines above to see if it's inside a middleware group
    $contextLines = [];
    $startIndex = max(0, $bankLineNumber - 20);
    $endIndex = min(count($lines) - 1, $bankLineNumber + 5);

    for ($i = $startIndex; $i <= $endIndex; $i++) {
        $contextLines[] = ($i + 1) . ": " . $lines[$i];
    }

    echo "\nContext around master/bank route:\n";
    echo "================================\n";
    foreach ($contextLines as $contextLine) {
        echo $contextLine . "\n";
    }

    // Check for middleware group
    for ($i = $bankLineNumber - 20; $i < $bankLineNumber; $i++) {
        if ($i >= 0 && isset($lines[$i])) {
            if (strpos($lines[$i], 'Route::middleware([') !== false) {
                // Look ahead for EnsureKaryawanPresent in next few lines
                for ($j = $i; $j < $i + 5 && $j < count($lines); $j++) {
                    if (strpos($lines[$j], 'EnsureKaryawanPresent') !== false) {
                        $middlewareGroupFound = true;
                        break 2;
                    }
                }
            }
            // Also check for ])->group(function () pattern
            if (strpos($lines[$i], '])->group(function ()') !== false) {
                // Check previous lines for middleware definition
                for ($k = max(0, $i - 10); $k < $i; $k++) {
                    if (strpos($lines[$k], 'EnsureKaryawanPresent') !== false) {
                        $middlewareGroupFound = true;
                        break 2;
                    }
                }
            }
        }
    }

    echo "\nAnalysis:\n";
    echo "=========\n";
    echo "Master/bank route exists: ✅ YES\n";
    echo "Inside middleware group: " . ($middlewareGroupFound ? "✅ YES" : "❌ NO") . "\n";

    if ($middlewareGroupFound) {
        echo "\n🎉 SUCCESS: master/bank route is properly protected with middleware!\n";
        echo "The route should now be accessible by authorized users.\n";
    } else {
        echo "\n⚠️  WARNING: master/bank route may not be properly protected!\n";
    }
} else {
    echo "❌ master/bank route not found in routes/web.php\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
