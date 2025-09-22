<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = App\Models\Permohonan::first();
if ($p) {
    echo 'Dari: ' . $p->dari . ', Ke: ' . $p->ke . PHP_EOL;
} else {
    echo 'Tidak ada data';
}