<?php

require_once __DIR__ . '/vendor/autoload.php';

echo "Testing fixed TandaTerimaExport class...\n";

try {
    $export = new \App\Exports\TandaTerimaExport([1]);
    echo "SUCCESS: TandaTerimaExport class can be instantiated\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}