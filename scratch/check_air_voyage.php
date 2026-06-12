<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

$items = BiayaKapal::whereHas('airDetails', function ($q) {
    $q->where('voyage', 'PS01JP26');
})->get();

echo 'Total matching records: '.$items->count()."\n";
foreach ($items as $item) {
    echo 'ID: '.$item->id."\n";
    echo 'No Invoice: '.$item->nomor_invoice."\n";
    echo 'Nominal: '.$item->nominal."\n";
    echo 'Total Biaya: '.$item->total_biaya."\n";

    $airDetails = $item->airDetails()->where('voyage', 'PS01JP26')->get();
    echo 'Air Details matching PS01JP26 count: '.$airDetails->count()."\n";
    foreach ($airDetails as $detail) {
        echo '  - Detail ID: '.$detail->id.', sub_total: '.$detail->sub_total.', grand_total: '.$detail->grand_total."\n";
    }
    echo "\n";
}
