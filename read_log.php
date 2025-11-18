<?php

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "Log file tidak ditemukan!\n";
    exit;
}

echo "=== LAST 50 LINES OF LARAVEL LOG ===\n\n";

$lines = file($logFile);
$lastLines = array_slice($lines, -50);

foreach ($lastLines as $line) {
    // Filter hanya log yang relevan
    if (stripos($line, 'DP') !== false || 
        stripos($line, 'tagihan') !== false || 
        stripos($line, 'Looking for') !== false ||
        stripos($line, 'Updated') !== false ||
        stripos($line, 'not found') !== false ||
        stripos($line, 'PEMBAYARAN AKTIVITAS') !== false) {
        echo $line;
    }
}
