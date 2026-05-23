<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapalTanto;

$tantoDetails = BiayaKapalTanto::orderBy('id', 'desc')->take(10)->get();

echo "ID | Kapal | Voyage | Kuantitas | Harga | Grand Total | Created At\n";
echo "--------------------------------------------------------------------\n";
foreach ($tantoDetails as $t) {
    echo "{$t->id} | {$t->kapal} | {$t->voyage} | {$t->kuantitas} | {$t->harga} | {$t->grand_total} | {$t->created_at}\n";
}
