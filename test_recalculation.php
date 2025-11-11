<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;

echo "=== TEST RECALCULATION COMMAND ===\n\n";

// Find a tagihan that is in a pranota
$tagihan = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')->first();

if (!$tagihan) {
    echo "❌ No tagihan found in pranota\n";
    exit(1);
}

echo "1. FOUND TAGIHAN\n";
echo "   Container: {$tagihan->nomor_kontainer}\n";
echo "   Pranota ID: {$tagihan->pranota_id}\n";
echo "   DPP: {$tagihan->dpp}\n";
echo "   Adjustment: {$tagihan->adjustment}\n";
echo "   PPN: {$tagihan->ppn}\n";
echo "   PPH: {$tagihan->pph}\n";
echo "   Grand Total (DB): {$tagihan->grand_total}\n";

$calculated = ($tagihan->dpp + $tagihan->adjustment) + $tagihan->ppn - $tagihan->pph;
echo "   Grand Total (Calculated): {$calculated}\n";

// Get pranota info before
$pranota = PranotaTagihanKontainerSewa::find($tagihan->pranota_id);

if (!$pranota) {
    echo "\n❌ Pranota ID {$tagihan->pranota_id} not found. Fixing tagihan...\n";
    $tagihan->pranota_id = null;
    $tagihan->status_pranota = null;
    $tagihan->saveQuietly();
    
    // Find another tagihan
    $tagihan = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')
        ->whereHas('pranota')
        ->first();
    
    if (!$tagihan) {
        echo "❌ No valid tagihan found in pranota\n";
        exit(1);
    }
    
    echo "✅ Found valid tagihan: {$tagihan->nomor_kontainer}\n";
    $pranota = $tagihan->pranota;
}

$pranotaTotalBefore = $pranota->total_amount;

echo "\n2. PRANOTA INFO (BEFORE)\n";
echo "   Pranota No: {$pranota->no_invoice}\n";
echo "   Total Amount: {$pranotaTotalBefore}\n";
echo "   Jumlah Tagihan: {$pranota->jumlah_tagihan}\n";

// Corrupt the data
echo "\n3. CORRUPTING DATA...\n";
$tagihan->grand_total = 999999.99;
$tagihan->saveQuietly();

echo "   Grand Total (Corrupted): " . $tagihan->fresh()->grand_total . "\n";

// Update pranota total to wrong value
$pranota->total_amount = 888888.88;
$pranota->save();
echo "   Pranota Total (Corrupted): " . $pranota->fresh()->total_amount . "\n";

echo "\n4. RUNNING RECALCULATION COMMAND...\n";
echo "   php artisan tagihan:recalculate-grand-total --force\n\n";

// Run the command
\Illuminate\Support\Facades\Artisan::call('tagihan:recalculate-grand-total', ['--force' => true]);
echo \Illuminate\Support\Facades\Artisan::output();

// Check results
$tagihanAfter = DaftarTagihanKontainerSewa::find($tagihan->id);
$pranotaAfter = PranotaTagihanKontainerSewa::find($pranota->id);

echo "\n5. RESULTS AFTER RECALCULATION\n";
echo "   Tagihan Grand Total: {$tagihanAfter->grand_total}\n";
echo "   Expected: {$calculated}\n";
echo "   Match: " . (abs($tagihanAfter->grand_total - $calculated) < 0.01 ? '✅ YES' : '❌ NO') . "\n";

echo "\n   Pranota Total Amount: {$pranotaAfter->total_amount}\n";

// Calculate expected pranota total
$expectedPranotaTotal = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->sum('grand_total');
echo "   Expected: {$expectedPranotaTotal}\n";
echo "   Match: " . (abs($pranotaAfter->total_amount - $expectedPranotaTotal) < 0.01 ? '✅ YES' : '❌ NO') . "\n";

echo "\n=== TEST COMPLETED ===\n";
