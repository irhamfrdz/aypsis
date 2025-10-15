<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Events\EventServiceProvider;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('daftar_tagihan_kontainer_sewa');
echo "Kolom daftar_tagihan_kontainer_sewa:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}