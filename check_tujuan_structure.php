<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Struktur tabel tujuans:\n";
echo "========================\n";

try {
    $columns = DB::select('DESCRIBE tujuans');

    foreach($columns as $column) {
        echo $column->Field . ' - ' . $column->Type;
        if ($column->Null == 'NO') {
            echo ' (NOT NULL)';
        }
        if ($column->Default !== null) {
            echo ' (Default: ' . $column->Default . ')';
        }
        echo "\n";
    }

    echo "\nCek apakah kolom uang_jalan ada:\n";
    $uangJalanExists = false;
    foreach($columns as $column) {
        if ($column->Field == 'uang_jalan') {
            $uangJalanExists = true;
            echo "✅ Kolom uang_jalan sudah ada!\n";
            break;
        }
    }

    if (!$uangJalanExists) {
        echo "❌ Kolom uang_jalan belum ada!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
