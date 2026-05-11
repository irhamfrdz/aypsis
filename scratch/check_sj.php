<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;

$no_sj = 'JB0033504';
$sj = SuratJalan::where('no_surat_jalan', $no_sj)->first();
if ($sj) {
    echo "SuratJalan: " . $sj->no_surat_jalan . "\n";
    echo "  Tujuan Pengambilan: " . $sj->tujuan_pengambilan . "\n";
    echo "  Tujuan Pengiriman: " . $sj->tujuan_pengiriman . "\n";
    echo "  Relation Pengambilan: " . ($sj->tujuanPengambilanRelation ? 'exists' : 'null') . "\n";
    echo "  Relation Pengiriman: " . ($sj->tujuanPengirimanRelation ? 'exists' : 'null') . "\n";
} else {
    $sjb = SuratJalanBongkaran::where('nomor_surat_jalan', $no_sj)->first();
    if ($sjb) {
        echo "SuratJalanBongkaran: " . $sjb->nomor_surat_jalan . "\n";
        echo "  Tujuan Pengambilan: " . $sjb->tujuan_pengambilan . "\n";
        echo "  Tujuan Pengiriman: " . $sjb->tujuan_pengiriman . "\n";
        echo "  Relation Pengambilan: " . ($sjb->tujuanPengambilanRelation ? 'exists' : 'null') . "\n";
        echo "  Relation Pengiriman: " . ($sjb->tujuanPengirimanRelation ? 'exists' : 'null') . "\n";
    } else {
        echo "Not found\n";
    }
}
