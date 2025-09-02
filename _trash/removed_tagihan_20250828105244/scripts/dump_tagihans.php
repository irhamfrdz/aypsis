<?php
// boots Laravel app minimally to use DB facade
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('tagihan_kontainer_sewa')->get();
foreach ($rows as $r) {
    echo sprintf("%d | %s | %s | %s\n", $r->id, $r->vendor ?? 'NULL', $r->tarif ?? 'NULL', $r->tanggal_harga_awal ?? 'NULL');
}
