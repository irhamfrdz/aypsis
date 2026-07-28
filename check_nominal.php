<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$records = App\Models\SuratJalanBongkaranBatam::orderBy('id', 'desc')->take(10)->get();
foreach($records as $r) {
    echo $r->id . ' | ' . $r->tujuan_pengambilan . ' | nominal: ' . $r->uang_jalan_nominal . "\n";
}
