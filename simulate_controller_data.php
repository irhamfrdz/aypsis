<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\Cache;

echo "=== SIMULATE CONTROLLER DATA ===\n\n";

// Simulate controller logic
$vendors = Cache::remember('tagihan_vendors', 300, function() {
    return DaftarTagihanKontainerSewa::distinct()
        ->whereNotNull('vendor')
        ->where('vendor', '!=', '')
        ->pluck('vendor')
        ->sort()
        ->values();
});

$sizes = Cache::remember('tagihan_sizes', 300, function() {
    return DaftarTagihanKontainerSewa::distinct()
        ->whereNotNull('size')
        ->where('size', '!=', '')
        ->pluck('size')
        ->sort()
        ->values();
});

$periodes = Cache::remember('tagihan_periodes', 300, function() {
    return DaftarTagihanKontainerSewa::distinct()
        ->whereNotNull('periode')
        ->pluck('periode')
        ->sort()
        ->values();
});

// Output exactly what controller sends to view
echo "Controller akan mengirim data berikut ke view:\n\n";

echo "\$vendors (collection):\n";
print_r($vendors->toArray());

echo "\n\$sizes (collection):\n";
print_r($sizes->toArray());

echo "\n\$periodes (collection):\n";
print_r($periodes->toArray());

// Test blade template logic
echo "\n=== TEST BLADE LOGIC ===\n";
echo "count(\$vendors ?? []): " . count($vendors ?? []) . "\n";
echo "count(\$sizes ?? []): " . count($sizes ?? []) . "\n";
echo "count(\$periodes ?? []): " . count($periodes ?? []) . "\n";

echo "\nForeach results:\n";
echo "Vendors:\n";
foreach ($vendors ?? ['ZONA', 'DPE'] as $vendor) {
    echo "  - $vendor\n";
}

echo "Sizes:\n";
foreach ($sizes ?? ['20', '40'] as $size) {
    echo "  - $size'\n";
}

echo "Periodes:\n";
foreach ($periodes ?? [] as $periode) {
    echo "  - Periode $periode\n";
}

?>
