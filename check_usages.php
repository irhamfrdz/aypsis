<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usages = App\Models\StockAmprahan::find(3788)->usages;
foreach ($usages as $u) {
    echo "Usage ID: {$u->id}, Jumlah: {$u->jumlah}, Tanggal: {$u->created_at}\n";
}
