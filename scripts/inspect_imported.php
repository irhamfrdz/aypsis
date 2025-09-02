<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

$total = DaftarTagihanKontainerSewa::count();
echo "Total rows in daftar_tagihan_kontainer_sewas: $total\n\n";

$rows = DaftarTagihanKontainerSewa::orderBy('tanggal_awal','desc')->limit(20)->get();
foreach ($rows as $r) {
    echo sprintf("%s;%s;%s;%s;%s;%s;%s\n", $r->vendor, $r->nomor_kontainer, $r->size, $r->group, $r->tanggal_awal, $r->periode, $r->dpp);
}
