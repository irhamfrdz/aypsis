<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fix Grand Total and Pranota with Correct Table ===\n\n";

// Calculate exact value: DPP + Adjustment + PPN - PPH
$dpp = 22522.53;
$adjustment = 10000.00;
$ppn = 3577.00;
$pph = 650.00;
$correctGrandTotal = $dpp + $adjustment + $ppn - $pph;

echo "Exact calculation:\n";
echo "DPP: Rp " . number_format($dpp, 2, ',', '.') . "\n";
echo "Adjustment: Rp " . number_format($adjustment, 2, ',', '.') . "\n";
echo "PPN: Rp " . number_format($ppn, 2, ',', '.') . "\n";
echo "PPH: Rp " . number_format($pph, 2, ',', '.') . "\n";
echo "Grand Total: Rp " . number_format($correctGrandTotal, 2, ',', '.') . "\n\n";

// Update tagihan Grand Total
DB::table('daftar_tagihan_kontainer_sewa')
    ->where('id', 1199)
    ->update([
        'grand_total' => $correctGrandTotal,
        'updated_at' => now()
    ]);

echo "✓ Updated tagihan grand_total!\n";

// Update pranotalist total_amount
DB::table('pranotalist')
    ->where('no_invoice', 'PTK12509000004')
    ->update([
        'total_amount' => $correctGrandTotal,
        'updated_at' => now()
    ]);

echo "✓ Updated pranotalist total_amount!\n";

echo "\n=== Verification ===\n";

// Verify tagihan
$tagihan = DB::table('daftar_tagihan_kontainer_sewa')->where('id', 1199)->first();
echo "Tagihan Grand Total: Rp " . number_format($tagihan->grand_total, 2, ',', '.') . "\n";

// Verify pranotalist
$pranota = DB::table('pranotalist')->where('no_invoice', 'PTK12509000004')->first();
echo "Pranota Total Amount: Rp " . number_format($pranota->total_amount, 2, ',', '.') . "\n";

echo "\n=== Both values now match at Rp " . number_format($correctGrandTotal, 2, ',', '.') . " ===\n";
echo "✓ Problem resolved!\n";
