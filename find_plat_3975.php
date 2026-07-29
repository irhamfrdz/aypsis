<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usages = \App\Models\StockAmprahanUsage::whereDate('tanggal_pengambilan', '2025-04-28')
    ->whereHas('stockAmprahan', function($q) {
        $q->where('nama_barang', 'like', '%PLAT 12%');
    })
    ->get();

foreach ($usages as $usage) {
    echo "ID: {$usage->id}, Barang: {$usage->stockAmprahan->nama_barang}, Qty: {$usage->jumlah}, Tanggal: {$usage->tanggal_pengambilan}\n";
}
