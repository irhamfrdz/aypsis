<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
\Illuminate\Foundation\Bootstrap\BootProviders::start(
    require __DIR__ . '/../bootstrap/app.php'
);

$rows = DB::table('tagihan_kontainer_sewa')->get();
foreach ($rows as $r) {
    echo sprintf("%d | %s | %s | %s\n", $r->id, $r->vendor ?? 'NULL', $r->tarif ?? 'NULL', $r->tanggal_harga_awal ?? 'NULL');
}
