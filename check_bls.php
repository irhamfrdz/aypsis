<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = Illuminate\Support\Facades\DB::table('bls')->where('no_voyage', 'PS01PJ26')->pluck('nomor_bl')->unique();
print_r($res->toArray());
