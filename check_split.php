<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;

echo "--- Searching for Tanda Terima with pattern 'LS1 0019578%' ---\n";
$tts = TandaTerimaLcl::withTrashed()
    ->where('nomor_tanda_terima', 'like', 'LS1 0019578%')
    ->get();

foreach ($tts as $tt) {
    echo "ID: {$tt->id}\n";
    echo "Nomor: {$tt->nomor_tanda_terima}\n";
    echo "Deleted At: {$tt->deleted_at}\n";
    echo "Status: {$tt->status}\n";
    echo "Created By: {$tt->created_by}\n";
    echo "Updated By: {$tt->updated_by}\n";
    
    $items = TandaTerimaLclItem::where('tanda_terima_lcl_id', $tt->id)->get();
    echo "Items Count: " . $items->count() . "\n";
    foreach ($items as $item) {
        echo "  - Item ID: {$item->id}\n";
        echo "    Nama: {$item->nama_barang}\n";
        echo "    Jumlah: {$item->jumlah} {$item->satuan}\n";
        echo "    Dimensi: {$item->panjang} x {$item->lebar} x {$item->tinggi}\n";
        echo "    Volume (meter_kubik): {$item->meter_kubik}\n";
        echo "    Tonase: {$item->tonase}\n";
    }
    echo "----------------------------------------\n";
}
