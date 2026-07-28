<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = App\Models\StockAmprahan::find(3788);
if ($s) {
    // Set harga satuan
    $s->harga_satuan = 9537500;
    // Kembalikan stock (jumlah) menjadi 0
    $s->jumlah = 0;
    $s->save();
    echo "Fixed StockAmprahan ID 3788: harga_satuan = 9537500, jumlah = 0.\n";
} else {
    echo "Record not found.\n";
}
