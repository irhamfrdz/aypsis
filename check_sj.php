<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sj = \App\Models\SuratJalan::where('no_surat_jalan', 'JB0031960')->first();
if ($sj) {
    print_r($sj->toArray());
} else {
    echo "Not found";
}
