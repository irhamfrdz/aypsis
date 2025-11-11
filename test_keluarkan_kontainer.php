<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;

echo "=== TEST KELUARKAN KONTAINER FEATURE ===\n\n";

// Find a pranota with tagihan
$pranota = PranotaTagihanKontainerSewa::whereHas('tagihanItems')->first();

if (!$pranota) {
    echo "❌ No pranota with tagihan found\n";
    exit(1);
}

echo "1. PRANOTA INFO (BEFORE)\n";
echo "   Pranota No: {$pranota->no_invoice}\n";
echo "   Status: {$pranota->status}\n";
echo "   Total Amount: {$pranota->total_amount}\n";
echo "   Jumlah Tagihan: {$pranota->jumlah_tagihan}\n";

// Get tagihan items
$tagihanItems = $pranota->tagihanItems;
echo "\n2. TAGIHAN ITEMS IN PRANOTA:\n";
foreach ($tagihanItems as $item) {
    echo "   - {$item->nomor_kontainer} (ID: {$item->id}) - Grand Total: {$item->grand_total}\n";
}

// Select first tagihan to remove
$tagihanToRemove = $tagihanItems->first();
echo "\n3. REMOVING TAGIHAN:\n";
echo "   Container: {$tagihanToRemove->nomor_kontainer}\n";
echo "   Grand Total: {$tagihanToRemove->grand_total}\n";
echo "   Status Pranota (Before): {$tagihanToRemove->status_pranota}\n";

// Simulate the lepas-kontainer endpoint logic
$tagihanIds = [$tagihanToRemove->id];

DaftarTagihanKontainerSewa::whereIn('id', $tagihanIds)->update([
    'pranota_id' => null,
    'status_pranota' => null
]);

// Update pranota
$remainingTagihanIds = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->pluck('id');

if ($remainingTagihanIds->count() > 0) {
    $pranota->total_amount = DaftarTagihanKontainerSewa::whereIn('id', $remainingTagihanIds)->sum('grand_total');
    $pranota->jumlah_tagihan = $remainingTagihanIds->count();
} else {
    $pranota->total_amount = 0;
    $pranota->jumlah_tagihan = 0;
}

$pranota->save();

echo "\n4. RESULTS AFTER REMOVAL:\n";

// Refresh data
$tagihanAfter = DaftarTagihanKontainerSewa::find($tagihanToRemove->id);
$pranotaAfter = PranotaTagihanKontainerSewa::find($pranota->id);

echo "   Tagihan Status:\n";
echo "     - pranota_id: " . ($tagihanAfter->pranota_id ?? 'NULL') . " (Expected: NULL) " . ($tagihanAfter->pranota_id === null ? '✅' : '❌') . "\n";
echo "     - status_pranota: " . ($tagihanAfter->status_pranota ?? 'NULL') . " (Expected: NULL) " . ($tagihanAfter->status_pranota === null ? '✅' : '❌') . "\n";

echo "\n   Pranota Status:\n";
echo "     - Total Amount: {$pranotaAfter->total_amount}\n";
echo "     - Jumlah Tagihan: {$pranotaAfter->jumlah_tagihan}\n";

$expectedTotal = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->sum('grand_total');
$expectedCount = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->count();

echo "     - Expected Total: {$expectedTotal}\n";
echo "     - Expected Count: {$expectedCount}\n";
echo "     - Total Match: " . (abs($pranotaAfter->total_amount - $expectedTotal) < 0.01 ? '✅' : '❌') . "\n";
echo "     - Count Match: " . ($pranotaAfter->jumlah_tagihan == $expectedCount ? '✅' : '❌') . "\n";

// Restore the tagihan back to pranota for cleanup
echo "\n5. RESTORING DATA (CLEANUP)...\n";
$tagihanAfter->pranota_id = $pranota->id;
$tagihanAfter->status_pranota = 'sudah_pranota';
$tagihanAfter->save();

$pranota->total_amount = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->sum('grand_total');
$pranota->jumlah_tagihan = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->count();
$pranota->save();

echo "   ✅ Data restored\n";

echo "\n=== TEST COMPLETED ✅ ===\n";
