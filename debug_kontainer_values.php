<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking actual database values:\n\n";

// Check stock_kontainers without alias
$stock = \App\Models\StockKontainer::select('nomor_seri_gabungan')
    ->limit(10)
    ->get();

echo "Stock Kontainers (raw):\n";
foreach($stock as $s) {
    $value = $s->nomor_seri_gabungan;
    echo "  - '" . $value . "' (length: " . strlen($value ?? '') . ", is null: " . (is_null($value) ? 'YES' : 'NO') . ")\n";
}

// Check kontainers without alias
$kont = \App\Models\Kontainer::select('nomor_seri_gabungan')
    ->limit(10)
    ->get();

echo "\nKontainers (raw):\n";
foreach($kont as $k) {
    $value = $k->nomor_seri_gabungan;
    echo "  - '" . $value . "' (length: " . strlen($value ?? '') . ", is null: " . (is_null($value) ? 'YES' : 'NO') . ")\n";
}

// Try with get() instead of pluck()
echo "\n=== Using get() and accessing attribute ===\n";
$stockData = \App\Models\StockKontainer::select('nomor_seri_gabungan as nomor_kontainer')
    ->whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->limit(5)
    ->get();

echo "Stock data using get():\n";
foreach($stockData as $item) {
    echo "  nomor_kontainer: '" . $item->nomor_kontainer . "'\n";
    echo "  nomor_seri_gabungan: '" . $item->nomor_seri_gabungan . "'\n";
    echo "  ---\n";
}
