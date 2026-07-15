<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\PembayaranPranotaOb::where('nomor_pembayaran', 'BTJ12607000099')->first();
$breakdown = $p->breakdown_supir;

foreach ($breakdown as &$b) {
    $b['dp'] = 300000;
    $b['sisa'] = $b['total_biaya'] - $b['dp'] - ($b['potongan_bpjs'] ?? 0) - ($b['potongan_utang'] ?? 0) - ($b['potongan_tabungan'] ?? 0);
    $b['grand_total'] = $b['sisa'];
}
$p->breakdown_supir = $breakdown;
$p->dp_amount = 1200000;
$p->save();

echo "Update successful\n";
echo json_encode($p->breakdown_supir, JSON_PRETTY_PRINT);
