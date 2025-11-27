<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo '=== STRUKTUR TABEL SURAT_JALAN_BONGKARANS ===' . PHP_EOL;
$columns = DB::select('DESCRIBE surat_jalan_bongkarans');
foreach($columns as $col) {
    echo $col->Field . ' - ' . $col->Type . ' - ' . ($col->Null === 'YES' ? 'NULL' : 'NOT NULL') . PHP_EOL;
}