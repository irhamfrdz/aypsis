<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = App\Models\PricelistUangJalanBatam::activeBbm()->get();
foreach($items as $item) {
    if ($item->wilayah) {
        $subWilayahs = array_map('trim', explode(',', $item->wilayah));
        foreach($subWilayahs as $w) {
            echo "'$w'\n";
        }
    }
}
