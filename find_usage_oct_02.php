<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usages = \App\Models\StockAmprahanUsage::whereDate('tanggal_pengambilan', '2025-10-02')
    ->whereHas('stockAmprahan', function($q) {
        $q->where('nama_barang', 'PLAT 12MM');
    })
    ->get();

foreach ($usages as $usage) {
    echo "ID: " . $usage->id . "\n";
    echo "Tanggal: " . $usage->tanggal_pengambilan . "\n";
    echo "Qty: " . $usage->qty_pemakaian . "\n";
    echo "Dikeluarkan Ke: " . $usage->di_keluarkan_ke . "\n";
    echo "--------------------------\n";
}
