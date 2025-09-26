<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$pranota = \App\Models\PranotaTagihanKontainerSewa::find(1);
$pranota->tagihan_kontainer_sewa_ids = [842, 863];
$pranota->jumlah_tagihan = 2;
$pranota->save();
echo 'Updated pranota 1\n';
?>
