<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

$item = BiayaKapal::with([
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
])->find(346);

foreach ([
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
] as $rel) {
    echo $rel.': '.$item->{$rel}->count()."\n";
}
