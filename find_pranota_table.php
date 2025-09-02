<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Find Pranota Table ===\n\n";

$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    if (strpos($tableName, 'pranota') !== false) {
        echo "Found: {$tableName}\n";
    }
}

echo "\n=== Search Complete ===\n";
