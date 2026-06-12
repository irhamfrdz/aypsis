<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

$item = BiayaKapal::with([
    'oppOptDetails',
])->where('nomor_invoice', 'BKP-05-26-000067')->first();

if (! $item) {
    echo "Invoice not found\n";
    exit;
}

echo 'ID: '.$item->id."\n";
echo 'Nomor Invoice: '.$item->nomor_invoice."\n";
echo 'Nama Kapal: '.json_encode($item->nama_kapal)."\n";
echo 'No Voyage: '.json_encode($item->no_voyage)."\n";
echo 'Nominal: '.$item->nominal."\n";
echo 'Total Biaya: '.$item->total_biaya."\n";
echo 'OppOptDetails count: '.$item->oppOptDetails->count()."\n";

foreach ($item->oppOptDetails as $detail) {
    echo '--- Detail ID: '.$detail->id." ---\n";
    echo 'Kapal: '.json_encode($detail->kapal)."\n";
    echo 'Voyage: '.json_encode($detail->voyage)."\n";
    echo 'Subtotal: '.$detail->subtotal."\n";
    echo 'Nominal: '.$detail->nominal."\n";
    echo 'Total Biaya: '.$detail->total_biaya."\n";
}
