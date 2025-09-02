<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== Database Schema Check ===\n\n";

$columns = Schema::getColumnListing('daftar_tagihan_kontainer_sewa');

echo "Available columns:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}

echo "\n=== Schema Complete ===\n";
