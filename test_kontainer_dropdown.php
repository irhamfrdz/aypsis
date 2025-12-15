<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing kontainer dropdown logic...\n\n";

$stock = \App\Models\StockKontainer::whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->select('nomor_seri_gabungan', 'ukuran')
    ->get()
    ->map(function($item) {
        return (object)[
            'nomor_seri_gabungan' => $item->nomor_seri_gabungan,
            'ukuran' => $item->ukuran
        ];
    });

echo "Stock Kontainers: " . $stock->count() . "\n";

$kon = \App\Models\Kontainer::whereNotNull('nomor_seri_gabungan')
    ->where('nomor_seri_gabungan', '!=', '')
    ->select('nomor_seri_gabungan', 'ukuran')
    ->get()
    ->map(function($item) {
        return (object)[
            'nomor_seri_gabungan' => $item->nomor_seri_gabungan,
            'ukuran' => $item->ukuran
        ];
    });

echo "Kontainers: " . $kon->count() . "\n";

$all = $stock->concat($kon);
echo "Concat Total: " . $all->count() . "\n";

$unique = $all->unique('nomor_seri_gabungan')->sortBy('nomor_seri_gabungan')->values();
echo "Unique Total: " . $unique->count() . "\n\n";

echo "First 10 unique kontainers:\n";
foreach($unique->take(10) as $k) {
    echo "- {$k->nomor_seri_gabungan} (ukuran: {$k->ukuran})\n";
}

echo "\n✅ Test completed!\n";
