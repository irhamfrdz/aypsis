<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = App\Models\StockAmprahan::withSum('usages', 'jumlah')->whereIn('id', [3776, 3777, 3781])->get(['id', 'harga_satuan', 'jumlah', 'adjustment']);
echo json_encode($s);
