<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usages = \App\Models\StockAmprahanUsage::with('stockAmprahan')
    ->whereHas('stockAmprahan', function ($q) {
        $q->where('nama_barang', 'like', '%PLAT 12MM%');
    })
    ->whereDate('tanggal_pengambilan', '2025-01-02')
    ->get();

foreach ($usages as $u) {
    echo "Usage ID: " . $u->id . " | Jumlah: " . $u->jumlah . " | Stock ID: " . $u->stock_amprahan_id . "\n";
    if ($u->stockAmprahan) {
        echo "Stock Harga Satuan: " . $u->stockAmprahan->harga_satuan . "\n";
    }
}
