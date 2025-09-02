<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fix Grand Total to Match Manual Calculation ===\n\n";

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

// Update Grand Total to exact calculation
DB::table('daftar_tagihan_kontainer_sewa')
    ->where('id', 1199)
    ->update([
        'grand_total' => $correctGrandTotal,
        'updated_at' => now()
    ]);

// Also update pranota total_amount
DB::table('pranota')
    ->where('no_invoice', 'PTK12509000004')
    ->update([
        'total_amount' => $correctGrandTotal,
        'updated_at' => now()
    ]);

echo "✓ Updated Grand Total and Pranota Total Amount to exact calculation!\n";
echo "✓ Both now show: Rp " . number_format($correctGrandTotal, 2, ',', '.') . "\n";

echo "\n=== Final Fix Complete ===\n";
