<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MasterPricelistSewaKontainer;

$vendor = 'DPE';
$size = 20;
$date = '2025-01-21';

try {
    $pr = MasterPricelistSewaKontainer::where('vendor', $vendor)
        ->where('ukuran_kontainer', (int)$size)
        ->where('tanggal_harga_awal', '<=', $date)
        ->where(function($q) use ($date) {
            $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $date);
        })
        ->orderBy('tanggal_harga_awal', 'desc')
        ->first();

    if ($pr) {
        echo "Found pricelist: vendor={$pr->vendor} ukuran={$pr->ukuran_kontainer} harga={$pr->harga} tarif={$pr->tarif} tanggal_awal={$pr->tanggal_harga_awal} tanggal_akhir={$pr->tanggal_harga_akhir}\n";
    } else {
        echo "No pricelist found for vendor={$vendor} ukuran={$size} date={$date}\n";
    }
} catch (Exception $e) {
    echo "Lookup error: " . $e->getMessage() . "\n";
}
