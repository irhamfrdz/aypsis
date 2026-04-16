<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach(\DB::select('SHOW TABLES') as $t) {
    $arr = (array)$t;
    $val = array_values($arr)[0];
    if (str_starts_with($val, 'pembayaran_') || str_starts_with($val, 'kas_')) {
        echo $val . "\n";
    }
}
