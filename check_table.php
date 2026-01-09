<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo \"=== Checking biaya_kapal_barang table structure ===\n\n\";

$columns = Schema::getColumnListing('biaya_kapal_barang');

echo \"Columns in biaya_kapal_barang table:\n\";
foreach ($columns as $column) {
    echo \"- $column\n\";
}

echo \"\n=== Checking if 'kapal' and 'voyage' columns exist ===\n\";
$hasKapal = Schema::hasColumn('biaya_kapal_barang', 'kapal');
$hasVoyage = Schema::hasColumn('biaya_kapal_barang', 'voyage');

echo \"Has 'kapal' column: \" . ($hasKapal ? 'YES' : 'NO') . \"\n\";
echo \"Has 'voyage' column: \" . ($hasVoyage ? 'YES' : 'NO') . \"\n\";

echo \"\n=== Detailed column information ===\n\";
$columnInfo = DB::select(\"DESCRIBE biaya_kapal_barang\");

foreach ($columnInfo as $column) {
    echo \"Column: {$column->Field}\n\";
    echo \"  Type: {$column->Type}\n\";
    echo \"  Null: {$column->Null}\n\";
    echo \"\n\";
}
