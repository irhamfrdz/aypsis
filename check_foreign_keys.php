<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$result = DB::select('SELECT CONSTRAINT_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = "pembayaran_pranota_perbaikan_kontainers" AND REFERENCED_TABLE_NAME IS NOT NULL');
foreach($result as $row) {
    echo $row->CONSTRAINT_NAME . ' -> ' . $row->COLUMN_NAME . PHP_EOL;
}
