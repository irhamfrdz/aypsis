<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

$item = BiayaKapal::where('nomor_invoice', 'BKP-05-26-000059')->first();

if (! $item) {
    echo "Invoice BKP-05-26-000059 not found\n";
    exit;
}

echo 'ID: '.$item->id."\n";
echo 'Nomor Invoice: '.$item->nomor_invoice."\n";
echo 'Nominal: '.$item->nominal."\n";
echo 'PPN: '.$item->ppn."\n";
echo 'PPh: '.$item->pph."\n";
echo 'Total Biaya: '.$item->total_biaya."\n";

echo 'barangDetails count: '.$item->barangDetails->count()."\n";
foreach ($item->barangDetails as $brg) {
    echo 'Barang ID: '.$brg->id.', Kapal: '.$brg->kapal.', Voyage: '.$brg->voyage.', subtotal: '.$brg->subtotal.', adjustment: '.$brg->adjustment."\n";
}

echo 'tenagaKerjaDetails count: '.$item->tenagaKerjaDetails->count()."\n";
foreach ($item->tenagaKerjaDetails as $tk) {
    echo 'Tenaga Kerja ID: '.$tk->id.', Kapal: '.$tk->kapal.', Voyage: '.$tk->voyage.', nominal: '.$tk->nominal."\n";
}
