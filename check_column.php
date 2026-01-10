<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking surat_jalan_bongkarans table structure...\n\n";

$result = DB::select("SHOW COLUMNS FROM surat_jalan_bongkarans WHERE Field = 'status_pembayaran_uang_rit'");

if (count($result) > 0) {
    $column = $result[0];
    echo "Column: " . $column->Field . "\n";
    echo "Type: " . $column->Type . "\n";
    echo "Null: " . $column->Null . "\n";
    echo "Key: " . $column->Key . "\n";
    echo "Default: " . $column->Default . "\n";
    echo "Extra: " . $column->Extra . "\n";
} else {
    echo "Column 'status_pembayaran_uang_rit' not found in surat_jalan_bongkarans table\n";
}

echo "\n\nChecking surat_jalans table for comparison...\n\n";

$result2 = DB::select("SHOW COLUMNS FROM surat_jalans WHERE Field = 'status_pembayaran_uang_rit'");

if (count($result2) > 0) {
    $column2 = $result2[0];
    echo "Column: " . $column2->Field . "\n";
    echo "Type: " . $column2->Type . "\n";
    echo "Null: " . $column2->Null . "\n";
    echo "Key: " . $column2->Key . "\n";
    echo "Default: " . $column2->Default . "\n";
    echo "Extra: " . $column2->Extra . "\n";
}
