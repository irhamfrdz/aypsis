<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;

echo "=== Test Pranota getTagihanItems Method ===\n\n";

try {
    // Find the pranota
    $pranota = Pranota::where('no_invoice', 'PTK12509000004')->first();

    if (!$pranota) {
        echo "❌ Pranota not found!\n";
        exit;
    }

    echo "✓ Found pranota: {$pranota->no_invoice}\n";
    echo "Tagihan IDs: " . json_encode($pranota->tagihan_ids) . "\n";

    // Test the tagihan method
    echo "\nTesting tagihan() method...\n";
    $tagihanItems = $pranota->tagihan();

    echo "✓ getTagihanItems() method works!\n";
    echo "Number of items: " . $tagihanItems->count() . "\n";

    if ($tagihanItems->count() > 0) {
        foreach ($tagihanItems as $item) {
            echo "- Tagihan ID {$item->id}: {$item->vendor} - Rp " . number_format($item->grand_total, 2, ',', '.') . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
