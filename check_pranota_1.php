<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$pranota = \App\Models\PranotaTagihanKontainerSewa::find(1);
echo 'Pranota 1 tagihan_ids: ' . json_encode($pranota->tagihan_kontainer_sewa_ids) . PHP_EOL;
?>
