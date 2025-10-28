<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;

// Test insert dengan dimension 5x5x5 = 125 m³
$lcl = TandaTerimaLcl::first();
$item = TandaTerimaLclItem::create([
    'tanda_terima_lcl_id' => $lcl->id,
    'item_number' => 999,
    'panjang' => 5.00,
    'lebar' => 5.00,
    'tinggi' => 5.00,
    'meter_kubik' => 125.0,
    'tonase' => 10.5
]);

echo "=== TEST AFTER MODEL FIX ===\n";
echo "ID: {$item->id}\n";
echo "Panjang: {$item->panjang}\n";
echo "Lebar: {$item->lebar}\n"; 
echo "Tinggi: {$item->tinggi}\n";
echo "Volume Input: 125.0\n";
echo "Volume Saved: {$item->meter_kubik}\n";
echo "Expected: 125.000000\n";
echo "Status: " . ($item->meter_kubik == 125.0 ? "✅ BERHASIL!" : "❌ GAGAL!") . "\n";
echo "============================\n";