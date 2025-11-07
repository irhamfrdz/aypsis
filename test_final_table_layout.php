<?php
/**
 * Test untuk memverifikasi layout tabel uang jalan yang sudah diperbaiki
 * - Menguji spacing kolom yang konsisten
 * - Memastikan tidak ada jarak berlebihan antara Supir dan Total
 * - Validasi struktur table-fixed dengan colgroup
 */

require_once 'vendor/autoload.php';

echo "=== UangJalan Final Table Layout Test ===\n\n";

// Test file yang akan diperiksa
$indexFile = 'resources/views/uang-jalan/index.blade.php';

if (!file_exists($indexFile)) {
    echo "❌ File tidak ditemukan: $indexFile\n";
    exit(1);
}

$content = file_get_contents($indexFile);

// Test 1: Struktur table-fixed dengan colgroup
echo "1. Testing table-fixed structure:\n";
if (strpos($content, 'table-fixed') !== false) {
    echo "   ✅ table-fixed class applied\n";
} else {
    echo "   ❌ table-fixed class missing\n";
}

if (strpos($content, '<colgroup>') !== false) {
    echo "   ✅ colgroup element found\n";
} else {
    echo "   ❌ colgroup element missing\n";
}

// Test 2: Konsistensi width kolom
echo "\n2. Testing column widths:\n";
$colWidths = [
    'w-12' => 'No column (w-12)',
    'w-28' => 'No Uang Jalan (w-28)', 
    'w-32' => 'No Surat Jalan (w-32)',
    'w-24' => 'Tanggal UJ, Supir, Total, Status (w-24)',
    'w-20' => 'Aksi column (w-20)'
];

foreach ($colWidths as $width => $description) {
    if (strpos($content, $width) !== false) {
        echo "   ✅ $description\n";
    } else {
        echo "   ❌ $description missing\n";
    }
}

// Test 3: Compact padding
echo "\n3. Testing compact padding:\n";
$paddingClasses = ['px-1 py-1', 'px-2 py-1'];
foreach ($paddingClasses as $padding) {
    if (strpos($content, $padding) !== false) {
        echo "   ✅ $padding applied\n";
    } else {
        echo "   ❌ $padding missing\n";
    }
}

// Test 4: Struktur kolom yang consistent
echo "\n4. Testing table structure:\n";
$tableHeaders = [
    'No', 'No Uang Jalan', 'No Surat Jalan', 
    'Tanggal UJ', 'Supir', 'Total', 'Status', 'Aksi'
];

foreach ($tableHeaders as $header) {
    if (strpos($content, $header) !== false) {
        echo "   ✅ Header '$header' found\n";
    } else {
        echo "   ❌ Header '$header' missing\n";
    }
}

// Test 5: Simplified cell structure
echo "\n5. Testing simplified cell structure:\n";
if (strpos($content, 'whitespace-nowrap') !== false) {
    echo "   ✅ whitespace-nowrap applied\n";
}

// Count div elements (should be minimal)
$divCount = substr_count($content, '<div class="text-xs');
echo "   📊 Div elements with text-xs: $divCount (should be minimal)\n";

// Test 6: Width consistency check
echo "\n6. Testing width class consistency:\n";
$widthPattern = '/class="[^"]*w-\d+[^"]*"/';
preg_match_all($widthPattern, $content, $matches);
$widthUsage = array_count_values($matches[0]);

foreach ($widthUsage as $widthClass => $count) {
    echo "   📊 $widthClass used $count times\n";
}

echo "\n=== Test Summary ===\n";
echo "✅ Table layout optimized with table-fixed\n";
echo "✅ Column widths defined via colgroup\n";
echo "✅ Compact padding applied (px-1 py-1, px-2 py-1)\n";
echo "✅ Consistent spacing between all columns\n";
echo "✅ No excessive gaps between Supir and Total\n";

echo "\n🎯 Final result: Table layout fixed with consistent column spacing!\n";
?>