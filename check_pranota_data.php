<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$pranotas = \App\Models\PranotaTagihanKontainerSewa::all();
foreach($pranotas as $p) {
    echo 'Pranota ' . $p->id . ': tagihan_ids = ' . json_encode($p->tagihan_kontainer_sewa_ids) . PHP_EOL;
}
?>
