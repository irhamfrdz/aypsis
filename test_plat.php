<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usages = \App\Models\StockAmprahanUsage::whereHas('stockAmprahan', function($q) {
    $q->where('nama_barang', 'PLAT 10MM');
})->take(5)->get();

foreach($usages as $u) {
    echo json_encode($u->toArray()) . "\n";
}
