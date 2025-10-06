<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "=== CEK DATA UNTUK FILTER DROPDOWN ===\n\n";

// Cek total data
$totalCount = DaftarTagihanKontainerSewa::count();
echo "Total data tagihan kontainer: $totalCount\n\n";

if ($totalCount == 0) {
    echo "❌ Tidak ada data di tabel, dropdown akan kosong!\n";
    echo "Silakan import data CSV terlebih dahulu.\n";
    exit;
}

// Cek vendors
echo "=== VENDORS ===\n";
$vendors = DaftarTagihanKontainerSewa::distinct()
    ->whereNotNull('vendor')
    ->where('vendor', '!=', '')
    ->pluck('vendor')
    ->sort()
    ->values();

echo "Vendors ditemukan: " . $vendors->count() . "\n";
foreach ($vendors as $vendor) {
    echo "- $vendor\n";
}
echo "\n";

// Cek sizes
echo "=== SIZES ===\n";
$sizes = DaftarTagihanKontainerSewa::distinct()
    ->whereNotNull('size')
    ->where('size', '!=', '')
    ->pluck('size')
    ->sort()
    ->values();

echo "Sizes ditemukan: " . $sizes->count() . "\n";
foreach ($sizes as $size) {
    echo "- $size\n";
}
echo "\n";

// Cek periodes
echo "=== PERIODES ===\n";
$periodes = DaftarTagihanKontainerSewa::distinct()
    ->whereNotNull('periode')
    ->pluck('periode')
    ->sort()
    ->values();

echo "Periodes ditemukan: " . $periodes->count() . "\n";
$periodeList = $periodes->take(10)->toArray(); // Take first 10
foreach ($periodeList as $periode) {
    echo "- Periode $periode\n";
}
if ($periodes->count() > 10) {
    echo "... dan " . ($periodes->count() - 10) . " periode lainnya\n";
}
echo "\n";

// Clear cache untuk testing
echo "=== CLEAR CACHE ===\n";
use Illuminate\Support\Facades\Cache;
Cache::forget('tagihan_vendors');
Cache::forget('tagihan_sizes');
Cache::forget('tagihan_periodes');
echo "✓ Cache cleared\n\n";

echo "=== STATUS ===\n";
if ($vendors->count() > 0 && $sizes->count() > 0 && $periodes->count() > 0) {
    echo "✅ Semua data filter tersedia, dropdown harus muncul!\n";
    echo "Jika masih tidak muncul, coba refresh halaman untuk clear cache browser.\n";
} else {
    echo "⚠️  Ada data filter yang kosong:\n";
    echo "- Vendors: " . ($vendors->count() > 0 ? "✅" : "❌") . "\n";
    echo "- Sizes: " . ($sizes->count() > 0 ? "✅" : "❌") . "\n";
    echo "- Periodes: " . ($periodes->count() > 0 ? "✅" : "❌") . "\n";
}

?>
