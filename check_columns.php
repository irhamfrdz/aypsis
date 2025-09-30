<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$result = DB::select("SHOW COLUMNS FROM pembayaran_pranota_perbaikan_kontainers WHERE Field = 'status'");
if (!empty($result)) {
    echo "Status column type: " . $result[0]->Type . PHP_EOL;
}
