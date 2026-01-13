<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Struktur Tabel biaya_kapals ===\n\n";

$columns = DB::select('DESCRIBE biaya_kapals');

echo str_pad("Field", 30) . " | " . str_pad("Type", 20) . " | " . str_pad("Null", 5) . " | Key\n";
echo str_repeat("-", 80) . "\n";

foreach($columns as $col) {
    echo str_pad($col->Field, 30) . " | " . 
         str_pad($col->Type, 20) . " | " . 
         str_pad($col->Null, 5) . " | " . 
         $col->Key . "\n";
}
