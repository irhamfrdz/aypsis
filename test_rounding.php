<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;

echo "=== TEST VOLUME ROUNDING ===\n";

$lcl = TandaTerimaLcl::first();

// Test 1: Volume bulat (harus jadi integer)
$item1 = TandaTerimaLclItem::create([
    'tanda_terima_lcl_id' => $lcl->id,
    'item_number' => 901,
    'panjang' => 10.00,
    'lebar' => 10.00,
    'tinggi' => 10.00,
    'meter_kubik' => 1000.000000, // Harus jadi 1000
]);

// Test 2: Volume desimal (harus tetap desimal)
$item2 = TandaTerimaLclItem::create([
    'tanda_terima_lcl_id' => $lcl->id,
    'item_number' => 902,
    'panjang' => 5.50,
    'lebar' => 3.25,
    'tinggi' => 2.75,
    'meter_kubik' => 49.1875, // Harus tetap desimal
]);

echo "Test 1 - Volume Bulat:\n";
echo "Input: 1000.000000\n";
echo "Saved: {$item1->meter_kubik}\n";
echo "Expected: 1000\n";
echo "Status: " . ($item1->meter_kubik == 1000 ? "✅ BERHASIL!" : "❌ GAGAL!") . "\n\n";

echo "Test 2 - Volume Desimal:\n";
echo "Input: 49.1875\n";
echo "Saved: {$item2->meter_kubik}\n";
echo "Expected: 49.1875\n";
echo "Status: " . ($item2->meter_kubik == 49.1875 ? "✅ BERHASIL!" : "❌ GAGAL!") . "\n\n";

// Test 3: Auto-calculation dari dimensi
$item3 = TandaTerimaLclItem::create([
    'tanda_terima_lcl_id' => $lcl->id,
    'item_number' => 903,
    'panjang' => 5.00,
    'lebar' => 4.00,
    'tinggi' => 3.00,
    // meter_kubik tidak diset - harus auto-calculate
]);

echo "Test 3 - Auto-calculate:\n";
echo "Dimensions: 5×4×3\n";
echo "Calculated: {$item3->meter_kubik}\n";
echo "Expected: 60\n";
echo "Status: " . ($item3->meter_kubik == 60 ? "✅ BERHASIL!" : "❌ GAGAL!") . "\n";

echo "=========================\n";