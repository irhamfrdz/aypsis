<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$permohonan = \App\Models\Permohonan::first();
if($permohonan) {
    echo 'Tujuan: ' . $permohonan->tujuan . PHP_EOL;
    echo 'Dari: ' . ($permohonan->dari ?? 'null') . PHP_EOL;
    echo 'Ke: ' . ($permohonan->ke ?? 'null') . PHP_EOL;
} else {
    echo 'Tidak ada data permohonan';
}
