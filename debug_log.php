<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Read recent laravel log for OB/manifest entries
$logFile = __DIR__ . '/storage/logs/laravel.log';
$lines = file($logFile);
$totalLines = count($lines);

// Get last 500 lines
$recentLines = array_slice($lines, max(0, $totalLines - 500));

$relevant = [];
foreach ($recentLines as $i => $line) {
    if (
        stripos($line, 'markAsOB') !== false ||
        stripos($line, 'manifest') !== false ||
        stripos($line, 'FCL/CARGO') !== false ||
        stripos($line, 'is_cargo') !== false ||
        stripos($line, 'Creating manifest') !== false ||
        stripos($line, 'ERROR saat') !== false ||
        stripos($line, 'Checking existing BL') !== false
    ) {
        $relevant[] = trim($line);
    }
}

if (empty($relevant)) {
    echo "No relevant log lines found in last 500 lines!\n";
    echo "This means markAsOB was NOT triggered at all or logs are not being written.\n";
    echo "\nLast 10 lines of log:\n";
    foreach (array_slice($recentLines, -10) as $line) {
        echo trim($line) . "\n";
    }
} else {
    echo "=== RELEVANT LOG ENTRIES ===\n";
    foreach (array_slice($relevant, -50) as $line) {
        // Strip long stack traces, only show meaningful parts
        if (strlen($line) > 300) {
            echo substr($line, 0, 300) . "...\n";
        } else {
            echo $line . "\n";
        }
    }
}
