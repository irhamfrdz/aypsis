<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing controller logic:\n\n";

// Simulate the controller logic
$stockKontainers = \App\Models\StockKontainer::select('nomor_seri_gabungan as nomor_kontainer')
    ->whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->distinct()
    ->orderBy('nomor_seri_gabungan')
    ->get()
    ->pluck('nomor_kontainer');

echo "Stock Kontainers count: " . $stockKontainers->count() . "\n";
echo "First 5 from stock:\n";
foreach($stockKontainers->take(5) as $s) {
    echo "  - $s\n";
}

$kontainers = \App\Models\Kontainer::select('nomor_seri_gabungan as nomor_kontainer')
    ->whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->distinct()
    ->orderBy('nomor_seri_gabungan')
    ->get()
    ->pluck('nomor_kontainer');

echo "\nKontainers count: " . $kontainers->count() . "\n";
echo "First 5 from kontainers:\n";
foreach($kontainers->take(5) as $k) {
    echo "  - $k\n";
}

// Gabungkan dan hilangkan duplikat
$availableKontainers = $stockKontainers->merge($kontainers)->unique()->sort()->values();

echo "\n=== FINAL RESULT ===\n";
echo "Total available kontainers: " . $availableKontainers->count() . "\n";
echo "Is collection: " . ($availableKontainers instanceof \Illuminate\Support\Collection ? 'YES' : 'NO') . "\n";
echo "Count > 0: " . ($availableKontainers->count() > 0 ? 'YES' : 'NO') . "\n";

echo "\nFirst 15 available kontainers:\n";
foreach($availableKontainers->take(15) as $k) {
    echo "  - $k\n";
}

// Test blade condition
echo "\n=== BLADE CONDITION TEST ===\n";
echo "isset(\$availableKontainers): " . (isset($availableKontainers) ? 'TRUE' : 'FALSE') . "\n";
echo "\$availableKontainers->count() > 0: " . ($availableKontainers->count() > 0 ? 'TRUE' : 'FALSE') . "\n";
echo "Blade @if would be: " . ((isset($availableKontainers) && $availableKontainers->count() > 0) ? 'TRUE (options shown)' : 'FALSE (no options)') . "\n";
