<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- voyages for %SEKAR% in 'bls' ---\n";
$items = DB::table('bls')
    ->where('nama_kapal', 'like', '%SEKAR%')
    ->select('nama_kapal', 'no_voyage', 'sudah_ob')
    ->distinct()
    ->get();

foreach($items as $i) {
    echo "- Kapal: '$i->nama_kapal' | Voyage: '$i->no_voyage' | Sudah OB: " . ($i->sudah_ob ? 'YA' : 'TIDAK') . "\n";
}
echo "\nTotal items found: " . $items->count() . "\n";
