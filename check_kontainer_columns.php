<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "=== KONTAINERS TABLE STRUCTURE ===\n\n";

$columns = DB::select('SHOW COLUMNS FROM kontainers');
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}

echo "\n=== STOCK_KONTAINERS TABLE STRUCTURE ===\n\n";

$stockColumns = DB::select('SHOW COLUMNS FROM stock_kontainers');
foreach ($stockColumns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}
