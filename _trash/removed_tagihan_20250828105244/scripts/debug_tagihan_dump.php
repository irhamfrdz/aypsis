<?php
// Quick script to dump first page of TagihanKontainerSewa with computed tanggal_checkpoint_supir
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TagihanKontainerSewa;

$items = TagihanKontainerSewa::orderBy('created_at','desc')->take(10)->get();
foreach ($items as $item) {
    echo "ID: {$item->id} | Vendor: {$item->vendor} | tanggal_harga_awal: ";
    echo ($item->tanggal_harga_awal instanceof \Carbon\Carbon) ? $item->tanggal_harga_awal->toDateString() : (string)$item->tanggal_harga_awal;
    echo " | tanggal_checkpoint_supir: ";
    echo (isset($item->tanggal_checkpoint_supir) && $item->tanggal_checkpoint_supir) ? ($item->tanggal_checkpoint_supir instanceof \Carbon\Carbon ? $item->tanggal_checkpoint_supir->toDateString() : (string)$item->tanggal_checkpoint_supir) : 'NULL';
    echo PHP_EOL;
}

echo "Done\n";
