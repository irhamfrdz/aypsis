<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usages = \App\Models\StockAmprahanUsage::where('stock_amprahan_id', 3781)->get(['id', 'jumlah', 'tanggal_pengambilan']);
echo json_encode($usages);
