<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapal;

$item = BiayaKapal::where('nomor_invoice', 'BKP-05-26-000059')->first();

echo 'Parent Nominal: '.$item->nominal."\n";
echo 'barangDetails sum for SP08BJ26: '.$item->barangDetails()->where('voyage', 'SP08BJ26')->sum('subtotal')."\n";
echo 'tenagaKerjaDetails sum for SP08BJ26: '.$item->tenagaKerjaDetails()->where('voyage', 'SP08BJ26')->sum('nominal')."\n";

echo 'barangDetails sum for PS01JP26: '.$item->barangDetails()->where('voyage', 'PS01JP26')->sum('subtotal')."\n";
echo 'tenagaKerjaDetails sum for PS01JP26: '.$item->tenagaKerjaDetails()->where('voyage', 'PS01JP26')->sum('nominal')."\n";
