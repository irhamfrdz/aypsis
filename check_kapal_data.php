<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo '=== NAMA KAPAL DI TABEL BL ===' . PHP_EOL;
$blKapals = DB::table('bls')->select('nama_kapal')->whereNotNull('nama_kapal')->distinct()->orderBy('nama_kapal')->get();
foreach($blKapals as $kapal) {
    echo '- ' . $kapal->nama_kapal . PHP_EOL;
}

echo PHP_EOL . '=== NAMA KAPAL DI TABEL MASTER_KAPALS ===' . PHP_EOL;
$masterKapals = DB::table('master_kapals')->select('nama_kapal')->orderBy('nama_kapal')->get();
foreach($masterKapals as $kapal) {
    echo '- ' . $kapal->nama_kapal . PHP_EOL;
}

echo PHP_EOL . '=== PERBEDAAN ===' . PHP_EOL;
$blNames = $blKapals->pluck('nama_kapal')->toArray();
$masterNames = $masterKapals->pluck('nama_kapal')->toArray();
$onlyInBl = array_diff($blNames, $masterNames);
$onlyInMaster = array_diff($masterNames, $blNames);

if (!empty($onlyInBl)) {
    echo 'Hanya di BL: ' . implode(', ', $onlyInBl) . PHP_EOL;
}
if (!empty($onlyInMaster)) {
    echo 'Hanya di Master: ' . implode(', ', $onlyInMaster) . PHP_EOL;
}