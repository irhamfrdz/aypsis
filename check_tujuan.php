<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$records = App\Models\SuratJalanBongkaranBatam::orderBy('id', 'desc')->take(10)->get(['id', 'nomor_surat_jalan', 'tujuan_pengambilan', 'tujuan_pengiriman']);
foreach($records as $r) {
    echo $r->id . ' | SJ: ' . $r->nomor_surat_jalan . ' | pengambilan: ' . ($r->tujuan_pengambilan ?? 'NULL') . ' | pengiriman: ' . ($r->tujuan_pengiriman ?? 'NULL') . "\n";
}
