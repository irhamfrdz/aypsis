<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$nomorKontainer = 'MSCU7196740';
$tagihans = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', $nomorKontainer)
    ->get();

echo "Data for $nomorKontainer:\n";
foreach ($tagihans as $t) {
    echo "ID: $t->id | P: $t->periode | Size: $t->size | DPP: $t->dpp | PPN: $t->ppn | PPH: $t->pph | GT: $t->grand_total\n";
}

echo "\nPricelist ZONA 20:\n";
print_r(DB::table('master_pricelist_sewa_kontainers')->where('vendor', 'ZONA')->where('ukuran_kontainer', '20')->first());

echo "\nPricelist ZONA 40:\n";
print_r(DB::table('master_pricelist_sewa_kontainers')->where('vendor', 'ZONA')->where('ukuran_kontainer', '40')->first());
