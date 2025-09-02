<?php

// Bootstrap Laravel
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p = \App\Models\Pranota::first();

// Get reflection to check actual method content
$reflection = new ReflectionClass($p);
$method = $reflection->getMethod('getStatusLabel');

// Get method source
$file = $method->getFileName();
$startLine = $method->getStartLine();
$endLine = $method->getEndLine();

echo "PHP reported file: $file\n";
echo "Current file: " . realpath(__DIR__ . '/app/Models/Pranota.php') . "\n";
echo "Files match: " . ($file === realpath(__DIR__ . '/app/Models/Pranota.php') ? 'YES' : 'NO') . "\n";

// Check file content directly
$currentFileContent = file_get_contents(__DIR__ . '/app/Models/Pranota.php');
$phpReportedContent = file_get_contents($file);

echo "\nFile sizes:\n";
echo "Current: " . strlen($currentFileContent) . " bytes\n";
echo "PHP reported: " . strlen($phpReportedContent) . " bytes\n";
echo "Content match: " . ($currentFileContent === $phpReportedContent ? 'YES' : 'NO') . "\n";

if ($currentFileContent !== $phpReportedContent) {
    echo "\nFILES ARE DIFFERENT!\n";

    // Show lines around where method should be
    $lines = explode("\n", $currentFileContent);
    echo "\nCurrent file lines 95-105:\n";
    for ($i = 95; $i <= 105 && $i < count($lines); $i++) {
        echo "$i: " . $lines[$i] . "\n";
    }

    $phpLines = explode("\n", $phpReportedContent);
    echo "\nPHP file lines 71-80:\n";
    for ($i = 71; $i <= 80 && $i < count($phpLines); $i++) {
        echo "$i: " . $phpLines[$i] . "\n";
    }
}

?>
