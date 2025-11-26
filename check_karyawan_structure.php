<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STRUKTUR TABLE KARYAWANS ===\n\n";

try {
    $columns = DB::select("SHOW COLUMNS FROM karyawans");
    foreach ($columns as $column) {
        echo "Column: {$column->Field}\n";
        echo "  Type: {$column->Type}\n";
        echo "  Null: {$column->Null}\n";
        echo "  Default: {$column->Default}\n";
        echo "  Extra: {$column->Extra}\n\n";
    }
    
    echo "\n=== SAMPLE DATA KARYAWAN ===\n";
    $sample = DB::select("SELECT * FROM karyawans LIMIT 5");
    if ($sample) {
        foreach ($sample as $row) {
            echo "Karyawan ID {$row->id}:\n";
            foreach ((array)$row as $key => $value) {
                echo "  {$key}: {$value}\n";
            }
            echo "\n";
        }
    } else {
        echo "Tidak ada data karyawan\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}