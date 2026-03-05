<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('naik_kapal');
foreach ($columns as $column) {
    $type = Schema::getColumnType('naik_kapal', $column);
    echo "Column: $column, Type: $type\n";
}
