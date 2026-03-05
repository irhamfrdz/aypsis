<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sj = \App\Models\SuratJalan::where('no_surat_jalan', 'JB0031960')->first();
echo "SJ " . $sj->no_surat_jalan . "\n";
echo "PENGIRIM: '" . $sj->pengirim . "'\n";
echo "PENGIRIM LEN: " . strlen($sj->pengirim) . "\n";
$p = \App\Models\Pengirim::where('nama_pengirim', $sj->pengirim)->first();
echo "Found: " . ($p ? 'YES ('.$p->nama_pengirim.')' : 'NO') . "\n";
