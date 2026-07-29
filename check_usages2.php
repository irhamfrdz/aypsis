<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usages = App\Models\StockAmprahanUsage::whereHas('stockAmprahan', function($q) { 
    $q->where('nomor_bukti', 'BCA25 0500120'); 
})->get();

foreach ($usages as $u) {
    echo "ID: {$u->id}, StockID: {$u->stock_amprahan_id}, Jumlah: {$u->jumlah}\n";
}
