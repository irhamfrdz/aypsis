<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

// Test update grand total
echo "=== TEST UPDATE GRAND TOTAL ===\n\n";

// Get first tagihan from pranota
$tagihan = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')
    ->whereHas('pranota', function($q) {
        $q->where('status', 'unpaid');
    })
    ->first();

if (!$tagihan) {
    die("Tidak ada tagihan dalam pranota unpaid untuk testing\n");
}

echo "Tagihan ID: {$tagihan->id}\n";
echo "Nomor Kontainer: {$tagihan->nomor_kontainer}\n";
echo "Grand Total Sekarang: " . number_format($tagihan->grand_total, 2) . "\n\n";

// Calculate new value (change last 3 digits to 600)
$oldGrandTotal = floatval($tagihan->grand_total);
$nilaiBulat = floor($oldGrandTotal);
$pecahan = round(($oldGrandTotal - $nilaiBulat) * 100);
$last3Digits = $nilaiBulat % 1000;
$nilaiRibu = floor($nilaiBulat / 1000);

echo "Breakdown:\n";
echo "- Nilai Ribu: " . number_format($nilaiRibu) . "\n";
echo "- Last 3 Digits: $last3Digits\n";
echo "- Pecahan: $pecahan\n\n";

// Change last 3 digits to 600
$newLast3Digits = 600;
$newNilaiBulat = ($nilaiRibu * 1000) + $newLast3Digits;
$newGrandTotal = $newNilaiBulat + ($pecahan / 100);

echo "New value:\n";
echo "- New Last 3 Digits: $newLast3Digits\n";
echo "- New Nilai Bulat: " . number_format($newNilaiBulat) . "\n";
echo "- New Grand Total: " . number_format($newGrandTotal, 2) . "\n\n";

// Try to update
echo "Attempting to update...\n";
try {
    DB::beginTransaction();
    
    $tagihan->grand_total = $newGrandTotal;
    $saved = $tagihan->save();
    
    echo "Save result: " . ($saved ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Reload from database
    $tagihan->refresh();
    echo "Grand Total after reload: " . number_format($tagihan->grand_total, 2) . "\n";
    echo "Difference: " . number_format($newGrandTotal - $tagihan->grand_total, 10) . "\n";
    
    // Check if value changed
    if (abs($tagihan->grand_total - $newGrandTotal) < 0.01) {
        echo "\n✅ Update berhasil!\n";
    } else {
        echo "\n❌ Nilai tidak berubah seperti yang diharapkan\n";
    }
    
    DB::rollBack();
    echo "\nTransaction rolled back (test only)\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
}
