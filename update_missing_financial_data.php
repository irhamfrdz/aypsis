<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "Updating existing records with missing financial data...\n\n";

// Find records with NULL financial values
$recordsToUpdate = DaftarTagihanKontainerSewa::whereNull('ppn')
    ->orWhereNull('pph')
    ->orWhereNull('grand_total')
    ->get();

echo "Found {$recordsToUpdate->count()} records with missing financial data\n\n";

$updated = 0;
$errors = 0;

foreach ($recordsToUpdate as $record) {
    try {
        // Get DPP and adjustment
        $dpp = (float)($record->dpp ?? 0);
        $adjustment = (float)($record->adjustment ?? 0);

        // Calculate adjusted DPP
        $adjustedDpp = $dpp + $adjustment;

        // Calculate PPN (12%)
        $ppn = round($adjustedDpp * 0.12, 2);

        // Calculate PPH (2%)
        $pph = round($adjustedDpp * 0.02, 2);

        // Calculate Grand Total
        $grandTotal = round($adjustedDpp + $ppn - $pph, 2);

        // Calculate DPP Nilai Lain if missing (11/12 dari adjusted DPP)
        $dppNilaiLain = $record->dpp_nilai_lain;
        if (empty($dppNilaiLain) || $dppNilaiLain == 0) {
            $dppNilaiLain = round($adjustedDpp * 11 / 12, 2);
        }

        // Update record
        $record->ppn = $ppn;
        $record->pph = $pph;
        $record->grand_total = $grandTotal;

        if (empty($record->dpp_nilai_lain) || $record->dpp_nilai_lain == 0) {
            $record->dpp_nilai_lain = $dppNilaiLain;
        }

        $record->save();

        $updated++;

        if ($updated <= 5) {
            echo "✓ Updated {$record->nomor_kontainer} Periode {$record->periode}:\n";
            echo "  DPP: " . number_format($dpp, 2) . "\n";
            echo "  Adjustment: " . number_format($adjustment, 2) . "\n";
            echo "  Adjusted DPP: " . number_format($adjustedDpp, 2) . "\n";
            echo "  PPN (12%): " . number_format($ppn, 2) . "\n";
            echo "  PPH (2%): " . number_format($pph, 2) . "\n";
            echo "  Grand Total: " . number_format($grandTotal, 2) . "\n";
            echo "  DPP Nilai Lain: " . number_format($dppNilaiLain, 2) . "\n\n";
        }

    } catch (\Exception $e) {
        $errors++;
        echo "✗ Error updating {$record->nomor_kontainer} Periode {$record->periode}: {$e->getMessage()}\n";
    }
}

echo "\n";
echo "====================================\n";
echo "Update Summary:\n";
echo "  Total records found: {$recordsToUpdate->count()}\n";
echo "  Successfully updated: {$updated}\n";
echo "  Errors: {$errors}\n";
echo "====================================\n\n";

// Verify update
echo "Verifying updates...\n";
$remainingNulls = DaftarTagihanKontainerSewa::whereNull('ppn')
    ->orWhereNull('pph')
    ->orWhereNull('grand_total')
    ->count();

if ($remainingNulls == 0) {
    echo "✓ All records now have complete financial data!\n";
} else {
    echo "⚠ Warning: {$remainingNulls} records still have NULL financial values\n";
}
