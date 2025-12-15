<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing FIXED controller logic:\n\n";

// Langsung pluck nomor_seri_gabungan tanpa alias
$stockKontainers = \App\Models\StockKontainer::whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->distinct()
    ->orderBy('nomor_seri_gabungan')
    ->pluck('nomor_seri_gabungan');

echo "Stock Kontainers count: " . $stockKontainers->count() . "\n";
echo "First 10 from stock:\n";
foreach($stockKontainers->take(10) as $s) {
    echo "  - '$s'\n";
}

$kontainers = \App\Models\Kontainer::whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->distinct()
    ->orderBy('nomor_seri_gabungan')
    ->pluck('nomor_seri_gabungan');

echo "\nKontainers count: " . $kontainers->count() . "\n";
echo "First 10 from kontainers:\n";
foreach($kontainers->take(10) as $k) {
    echo "  - '$k'\n";
}

// Gabungkan dan hilangkan duplikat
$availableKontainers = $stockKontainers->merge($kontainers)->unique()->sort()->values();

echo "\n=== FINAL RESULT ===\n";
echo "Total available kontainers: " . $availableKontainers->count() . "\n";
echo "Count > 0: " . ($availableKontainers->count() > 0 ? 'YES ✓' : 'NO ✗') . "\n";

echo "\nFirst 20 available kontainers:\n";
foreach($availableKontainers->take(20) as $k) {
    echo "  - '$k'\n";
}

echo "\n✅ DROPDOWN WILL NOW SHOW " . $availableKontainers->count() . " CONTAINERS!\n";
