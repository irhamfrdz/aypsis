<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = App\Models\StockAmprahan::find(3788);
if ($s) {
    $s->harga_satuan = 9537500;
    $s->jumlah = 58;
    $s->save();
    echo "Updated StockAmprahan ID 3788 successfully.\n";
} else {
    echo "Record not found.\n";
}
