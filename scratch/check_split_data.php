<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;

$tt = TandaTerimaLcl::withTrashed()
    ->where('nomor_tanda_terima', 'LIKE', '%0019578%')
    ->get();

echo "Matching Tanda Terima LCL:\n";
foreach ($tt as $t) {
    echo "ID: {$t->id} | No: {$t->nomor_tanda_terima} | Deleted At: {$t->deleted_at} | Status: {$t->status}\n";
    $items = TandaTerimaLclItem::where('tanda_terima_lcl_id', $t->id)->get();
    echo "  Items:\n";
    foreach ($items as $item) {
        echo "    ID: {$item->id} | Nama: {$item->nama_barang} | Qty: {$item->jumlah} | Vol: {$item->meter_kubik} | Berat: {$item->tonase} | Deleted At: {$item->deleted_at}\n";
    }
}
