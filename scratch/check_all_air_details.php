<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

$item = BiayaKapal::find(350);

echo 'ID: '.$item->id."\n";
echo 'No Invoice: '.$item->nomor_invoice."\n";
echo 'Nominal: '.$item->nominal."\n";

echo "All Air Details:\n";
foreach ($item->airDetails as $detail) {
    echo 'Detail ID: '.$detail->id.', Kapal: '.$detail->kapal.', Voyage: '.$detail->voyage.', sub_total: '.$detail->sub_total.', grand_total: '.$detail->grand_total."\n";
}
