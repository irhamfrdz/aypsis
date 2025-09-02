<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\Pranota;

echo "=== Fix Database Values Based on UI ===\n\n";

// Based on screenshot UI values:
// PPN: Rp 3,577
// PPH: Rp 650
// Grand Total: Rp 35,450

$tagihan = DaftarTagihanKontainerSewa::find(1199);
$dpp = 22522.53;
$adjustment = 10000.00;
$ppnNilai = 3577.00;  // From UI
$pphNilai = 650.00;   // From UI
$correctGrandTotal = 35450.00; // From UI

echo "Updating tagihan ID 1199 with correct values from UI...\n";
echo "DPP: Rp " . number_format($dpp, 2, ',', '.') . "\n";
echo "Adjustment: Rp " . number_format($adjustment, 2, ',', '.') . "\n";
echo "PPN Nilai: Rp " . number_format($ppnNilai, 2, ',', '.') . "\n";
echo "PPH Nilai: Rp " . number_format($pphNilai, 2, ',', '.') . "\n";
echo "Grand Total: Rp " . number_format($correctGrandTotal, 2, ',', '.') . "\n\n";

// Calculate PPN and PPH percentages
$ppnBase = $dpp + $adjustment; // 32,522.53
$ppnPersen = ($ppnNilai / $ppnBase) * 100; // 3577 / 32522.53 ≈ 11%
$pphPersen = ($pphNilai / $dpp) * 100; // 650 / 22522.53 ≈ 2.89%

echo "Calculated percentages:\n";
echo "PPN Persen: " . round($ppnPersen, 2) . "%\n";
echo "PPH Persen: " . round($pphPersen, 2) . "%\n\n";

// Update the database
$tagihan->update([
    'ppn_persen' => round($ppnPersen, 2),
    'ppn_nilai' => $ppnNilai,
    'pph_persen' => round($pphPersen, 2),
    'pph_nilai' => $pphNilai,
    'grand_total' => $correctGrandTotal
]);

echo "✓ Tagihan updated successfully!\n\n";

// Update pranota total_amount
$pranota = Pranota::where('no_invoice', 'PTK12509000004')->first();
$pranota->update(['total_amount' => $correctGrandTotal]);

echo "✓ Pranota total_amount updated to match!\n\n";

echo "=== Verification ===\n";
$tagihan->fresh();
echo "New Grand Total (DB): Rp " . number_format((float)$tagihan->grand_total, 2, ',', '.') . "\n";
echo "New PPN: Rp " . number_format((float)$tagihan->ppn_nilai, 2, ',', '.') . " ({$tagihan->ppn_persen}%)\n";
echo "New PPH: Rp " . number_format((float)$tagihan->pph_nilai, 2, ',', '.') . " ({$tagihan->pph_persen}%)\n";

$pranota->fresh();
echo "Pranota Total Amount: Rp " . number_format((float)$pranota->total_amount, 2, ',', '.') . "\n";

echo "\n=== Fix Complete ===\n";
