<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pranota;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== Debug Pranota vs Tagihan Values ===\n\n";

// Get the pranota
$pranota = Pranota::where('no_invoice', 'PTK12509000004')->first();

if (!$pranota) {
    echo "Pranota not found!\n";
    exit;
}

echo "Pranota: {$pranota->no_invoice}\n";
echo "Pranota Total Amount: Rp " . number_format((float)$pranota->total_amount, 2, ',', '.') . "\n";
echo "Jumlah Tagihan: {$pranota->jumlah_tagihan}\n";
echo "Tagihan IDs: " . json_encode($pranota->tagihan_ids) . "\n\n";

// Get the tagihan details
if (!empty($pranota->tagihan_ids)) {
    foreach ($pranota->tagihan_ids as $tagihanId) {
        $tagihan = DaftarTagihanKontainerSewa::find($tagihanId);

        if ($tagihan) {
            echo "--- Tagihan ID: {$tagihanId} ---\n";
            echo "DPP: Rp " . number_format($tagihan->dpp ?? 0, 2, ',', '.') . "\n";
            echo "Adjustment: Rp " . number_format($tagihan->adjustment ?? 0, 2, ',', '.') . "\n";
            echo "PPN Nilai: Rp " . number_format($tagihan->ppn_nilai ?? 0, 2, ',', '.') . "\n";
            echo "PPH Nilai: Rp " . number_format($tagihan->pph_nilai ?? 0, 2, ',', '.') . "\n";
            echo "Grand Total (database): Rp " . number_format($tagihan->grand_total ?? 0, 2, ',', '.') . "\n";

            // Manual calculation
            $calculatedTotal = ($tagihan->dpp ?? 0) + ($tagihan->adjustment ?? 0) + ($tagihan->ppn_nilai ?? 0) - ($tagihan->pph_nilai ?? 0);
            echo "Calculated Total (DPP + Adj + PPN - PPH): Rp " . number_format($calculatedTotal, 2, ',', '.') . "\n\n";
        }
    }
}

echo "=== Analysis Complete ===\n";
