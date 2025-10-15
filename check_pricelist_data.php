<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Events\EventServiceProvider;
use App\Models\MasterPricelistSewaKontainer;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SAMPLE DATA MASTER PRICELIST ===\n";
$data = MasterPricelistSewaKontainer::take(10)->get();
foreach($data as $item) {
    echo "ID: {$item->id}\n";
    echo "Vendor: {$item->vendor}\n";
    echo "Ukuran: {$item->ukuran_kontainer}\n";
    echo "Tarif: {$item->tarif}\n";
    echo "Harga: {$item->harga}\n";
    echo "---\n";
}