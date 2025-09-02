<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Struktur Kolom daftar_tagihan_kontainer_sewa ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('daftar_tagihan_kontainer_sewa');
print_r($columns);

echo "\n=== Contoh Data ===\n";
$data = DB::table('daftar_tagihan_kontainer_sewa')->first();
if ($data) {
    foreach ((array)$data as $key => $value) {
        if (strpos(strtolower($key), 'tarif') !== false) {
            echo "$key: $value\n";
        }
    }
}

echo "\n=== Semua Field yang Mengandung 'tarif' ===\n";
foreach ($columns as $column) {
    if (strpos(strtolower($column), 'tarif') !== false) {
        echo "- $column\n";
    }
}
