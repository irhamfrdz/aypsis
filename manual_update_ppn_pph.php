<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== Manual Update PPN/PPH Values ===\n\n";

// Direct database update to avoid model issues
DB::table('daftar_tagihan_kontainer_sewa')
    ->where('id', 1199)
    ->update([
        'ppn_persen' => 11.00,
        'ppn_nilai' => 3577.00,
        'pph_persen' => 2.89,
        'pph_nilai' => 650.00,
        'grand_total' => 35450.00,
        'updated_at' => now()
    ]);

echo "✓ Direct database update completed!\n\n";

// Verify
$tagihan = DaftarTagihanKontainerSewa::find(1199);
echo "=== Verification ===\n";
echo "PPN: {$tagihan->ppn_persen}% = Rp " . number_format((float)$tagihan->ppn_nilai, 2, ',', '.') . "\n";
echo "PPH: {$tagihan->pph_persen}% = Rp " . number_format((float)$tagihan->pph_nilai, 2, ',', '.') . "\n";
echo "Grand Total: Rp " . number_format((float)$tagihan->grand_total, 2, ',', '.') . "\n";

echo "\n=== Update Complete ===\n";
