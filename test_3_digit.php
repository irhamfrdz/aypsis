<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;

echo "=== TEST 3 DIGIT DESIMAL ===\n";

$lcl = TandaTerimaLcl::first();

// Test 1: Volume bulat - harus jadi 1000.000
$item1 = TandaTerimaLclItem::create([
    'tanda_terima_lcl_id' => $lcl->id,
    'item_number' => 801,
    'panjang' => 10.00,
    'lebar' => 10.00,
    'tinggi' => 10.00,
    'meter_kubik' => 1000.0,
]);

// Test 2: Volume dengan 3 desimal - harus jadi 49.188
$item2 = TandaTerimaLclItem::create([
    'tanda_terima_lcl_id' => $lcl->id,
    'item_number' => 802,
    'panjang' => 5.50,
    'lebar' => 3.25,
    'tinggi' => 2.75,
    'meter_kubik' => 49.1875, // Harus dibulatkan ke 49.188
]);

// Test 3: Volume dengan banyak desimal - harus dipotong ke 3 digit
$item3 = TandaTerimaLclItem::create([
    'tanda_terima_lcl_id' => $lcl->id,
    'item_number' => 803,
    'panjang' => 2.33,
    'lebar' => 1.77,
    'tinggi' => 0.95,
    'meter_kubik' => 3.9238455, // Harus jadi 3.924
]);

echo "Test 1 - Volume Bulat:\n";
echo "Input: 1000.0\n";
echo "Saved: {$item1->meter_kubik}\n";
echo "Expected: 1000.000\n";
echo "Status: " . (number_format($item1->meter_kubik, 3) == "1000.000" ? "✅ BERHASIL!" : "❌ GAGAL!") . "\n\n";

echo "Test 2 - Volume 3 Desimal:\n";
echo "Input: 49.1875\n";
echo "Saved: {$item2->meter_kubik}\n";
echo "Expected: 49.188\n";
echo "Status: " . (number_format($item2->meter_kubik, 3) == "49.188" ? "✅ BERHASIL!" : "❌ GAGAL!") . "\n\n";

echo "Test 3 - Volume Banyak Desimal:\n";
echo "Input: 3.9238455\n";
echo "Saved: {$item3->meter_kubik}\n";
echo "Expected: 3.924\n";
echo "Status: " . (number_format($item3->meter_kubik, 3) == "3.924" ? "✅ BERHASIL!" : "❌ GAGAL!") . "\n\n";

echo "============================\n";