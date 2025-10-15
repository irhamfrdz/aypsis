<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Events\EventServiceProvider;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('master_pricelist_sewa_kontainers');
echo "Kolom master_pricelist_sewa_kontainers:\n";
foreach ($columns as $column) {
    echo "- {$column}\n";
}
