<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== Fix Database Values with Correct Columns ===\n\n";

// Direct database update with correct column names
DB::table('daftar_tagihan_kontainer_sewa')
    ->where('id', 1199)
    ->update([
        'ppn' => 3577.00,    // PPN value from UI
        'pph' => 650.00,     // PPH value from UI
        'grand_total' => 35450.00,  // Grand Total from UI
        'updated_at' => now()
    ]);

echo "✓ Updated with correct column names!\n\n";

// Verify
$tagihan = DaftarTagihanKontainerSewa::find(1199);
echo "=== Verification ===\n";
echo "DPP: Rp " . number_format((float)$tagihan->dpp, 2, ',', '.') . "\n";
echo "Adjustment: Rp " . number_format((float)$tagihan->adjustment, 2, ',', '.') . "\n";
echo "PPN: Rp " . number_format((float)$tagihan->ppn, 2, ',', '.') . "\n";
echo "PPH: Rp " . number_format((float)$tagihan->pph, 2, ',', '.') . "\n";
echo "Grand Total: Rp " . number_format((float)$tagihan->grand_total, 2, ',', '.') . "\n\n";

// Manual calculation verification
$manual = $tagihan->dpp + $tagihan->adjustment + $tagihan->ppn - $tagihan->pph;
echo "Manual calculation: Rp " . number_format($manual, 2, ',', '.') . "\n";
echo "Match with Grand Total: " . (abs($manual - $tagihan->grand_total) < 0.01 ? "YES" : "NO") . "\n";

echo "\n=== Update Complete ===\n";
