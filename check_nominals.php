<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$biaya = \DB::table('biaya_kapals')->where('nomor_invoice', 'BKP-02-26-000044')->first();
$doks = \DB::table('biaya_kapal_dokumens')->where('biaya_kapal_id', $biaya->id)->get();
echo "BiayaKapal Total Nominal: " . $biaya->nominal . "\n";
foreach($doks as $d) {
    echo $d->kapal . ' - ' . $d->voyage . ' nominal: ' . $d->nominal . ' total: ' . $d->total_biaya . "\n";
}
