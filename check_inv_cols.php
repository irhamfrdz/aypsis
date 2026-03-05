<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "StockKontainer columns:\n";
print_r(Schema::getColumnListing('stock_kontainers'));

echo "\nKontainer columns:\n";
print_r(Schema::getColumnListing('kontainers'));
