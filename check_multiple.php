<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stocks = App\Models\StockAmprahan::where('nomor_bukti', 'BCA25 0500120')->where('nama_barang', 'PLAT 10MM')->get();
foreach ($stocks as $s) {
    $usages = $s->usages()->sum('jumlah');
    echo "ID: {$s->id}, Jumlah (Stock): {$s->jumlah}, Total Usages: {$usages}\n";
}
