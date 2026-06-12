<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

$item = BiayaKapal::where('nomor_invoice', 'BKP-05-26-000067')->first();
if (! $item) {
    echo "Invoice BKP-05-26-000067 not found!\n";
    exit;
}

echo 'Invoice ID: '.$item->id."\n";
echo 'Nominal: '.$item->nominal."\n";
echo 'PPN: '.$item->ppn."\n";
echo 'PPh: '.$item->pph."\n";
echo 'Total Biaya: '.$item->total_biaya."\n";
echo 'Jenis Biaya: '.$item->jenis_biaya."\n";

$relations = [
    'barangDetails',
    'airDetails',
    'tkbmDetails',
    'truckingDetails',
    'stuffingDetails',
    'perlengkapanDetails',
    'labuhTambatDetails',
    'oppOptDetails',
    'thcDetails',
    'loloDetails',
    'storageDetails',
    'freightDetails',
    'perijinanDetails',
    'meratusDetails',
    'temasDetails',
    'tantoDetails',
    'demurrageDetails',
    'tenagaKerjaDetails',
];

foreach ($relations as $rel) {
    $count = $item->{$rel}()->count();
    if ($count > 0) {
        echo "Relation '{$rel}' has {$count} records:\n";
        foreach ($item->{$rel} as $row) {
            echo '  - Kapal: '.($row->kapal ?? 'N/A').' | Voyage: '.($row->voyage ?? 'N/A');
            echo ' | Subtotal: '.($row->subtotal ?? 'N/A').' | Tarif: '.($row->tarif ?? 'N/A').' | Jumlah: '.($row->jumlah ?? 'N/A')."\n";
        }
    }
}
