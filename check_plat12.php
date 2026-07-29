<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Usages:\n";
$usages = App\Models\StockAmprahanUsage::whereHas('stockAmprahan', function($q) {
    $q->where('nama_barang', 'like', '%PLAT 12MM%');
})->with('stockAmprahan')->get();

foreach($usages as $u) {
    echo 'Usage ID: ' . $u->id . ' | Stock ID: ' . $u->stock_amprahan_id . ' | Date: ' . $u->tanggal_pengambilan . ' | Qty: ' . $u->jumlah . ' | Harga Satuan: ' . $u->stockAmprahan->harga_satuan . " | Total Nilai Keluar: " . ($u->jumlah * $u->stockAmprahan->harga_satuan) . "\n";
}
