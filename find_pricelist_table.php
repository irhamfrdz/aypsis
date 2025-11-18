<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Mencari tabel pricelist...\n\n";

$tables = DB::select('SHOW TABLES');
$pricelistTables = [];

foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    if (stripos($tableName, 'pricelist') !== false || stripos($tableName, 'price') !== false) {
        $pricelistTables[] = $tableName;
        echo "Ditemukan: {$tableName}\n";
    }
}

if (empty($pricelistTables)) {
    echo "\nTidak ada tabel dengan kata 'pricelist'. Mencari tabel dengan kata 'kontainer'...\n\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (stripos($tableName, 'kontainer') !== false || stripos($tableName, 'sewa') !== false) {
            echo "  - {$tableName}\n";
        }
    }
}
