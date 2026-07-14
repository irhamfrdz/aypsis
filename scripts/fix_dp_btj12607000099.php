<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\PembayaranPranotaOb::where('nomor_pembayaran', 'BTJ12607000099')->first();
$breakdown = $p->breakdown_supir;

foreach ($breakdown as &$b) {
    // Reset all potongan to 0 as requested by the 2.880.000 target sisa
    $b['potongan_bpjs'] = 0;
    $b['potongan_utang'] = 0;
    $b['potongan_tabungan'] = 0;
    
    // Recalculate sisa
    $b['sisa'] = $b['total_biaya'] - $b['dp'] - ($b['potongan_bpjs'] ?? 0) - ($b['potongan_utang'] ?? 0) - ($b['potongan_tabungan'] ?? 0);
    $b['grand_total'] = $b['sisa'];
}
$p->breakdown_supir = $breakdown;
$p->total_pembayaran = 2880000;
$p->total_setelah_penyesuaian = 2880000;
$p->save();

echo "Update successful\n";
echo json_encode($p->breakdown_supir, JSON_PRETTY_PRINT);
