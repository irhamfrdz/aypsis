<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TujuanKegiatanUtama;
use App\Models\SuratJalan;
use App\Models\PranotaOngkosTruk;

$tujuan = TujuanKegiatanUtama::first();
echo "TujuanKegiatanUtama columns: " . implode(', ', array_keys($tujuan->getAttributes())) . "\n";

$pranota = PranotaOngkosTruk::with('items.suratJalan')->latest()->first();
if ($pranota) {
    echo "Pranota: " . $pranota->no_pranota . "\n";
    foreach ($pranota->items as $item) {
        $sj = $item->suratJalan;
        if ($sj) {
            echo "SJ: " . $sj->no_surat_jalan . " | Tujuan Pengambilan: " . $sj->tujuan_pengambilan . " | Tujuan Pengiriman: " . $sj->tujuan_pengiriman . "\n";
            if ($sj->tujuanPengambilanRelation) {
                echo "  Relation exists! Attributes: " . implode(', ', array_keys($sj->tujuanPengambilanRelation->getAttributes())) . "\n";
            } else {
                echo "  Relation is NULL\n";
            }
        }
    }
}
