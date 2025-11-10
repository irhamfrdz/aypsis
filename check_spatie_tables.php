<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking permission related tables:\n";

$tables = DB::select('SHOW TABLES LIKE "%permission%"');
foreach($tables as $table) {
    $column = array_values((array)$table)[0];
    echo "- " . $column . "\n";
}

$tables2 = DB::select('SHOW TABLES LIKE "%role%"');
foreach($tables2 as $table) {
    $column = array_values((array)$table)[0];
    echo "- " . $column . "\n";
}