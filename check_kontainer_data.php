<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Sample Stock Kontainers:\n";
$stock = \App\Models\StockKontainer::select('nomor_seri_gabungan')
    ->whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->limit(10)
    ->pluck('nomor_seri_gabungan');
    
foreach($stock as $s) {
    echo "- $s\n";
}

echo "\nSample Kontainers:\n";
$kontainers = \App\Models\Kontainer::select('nomor_seri_gabungan')
    ->whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->limit(10)
    ->pluck('nomor_seri_gabungan');
    
foreach($kontainers as $k) {
    echo "- $k\n";
}

echo "\nMerged and unique:\n";
$merged = $stock->merge($kontainers)->unique()->sort()->values();
echo "Total unique kontainers: " . $merged->count() . "\n";
echo "First 10:\n";
foreach($merged->take(10) as $m) {
    echo "- $m\n";
}
