<?php
// scripts/update_zona20ft_harian.php
// Usage: php scripts/update_zona20ft_harian.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;

echo "Starting update for ZONA 20ft Harian using daily rate 22,522.53\n";
$dailyRate = 22522.53;
$updated = 0;
$skipped = 0;
$errors = 0;
$details = [];

// Create a safe backup table before making changes
$timestamp = date('Ymd_His');
$backupTable = 'daftar_tagihan_kontainer_sewa_backup_' . $timestamp;
try {
    echo "Creating backup table: $backupTable\n";
    DB::statement("CREATE TABLE IF NOT EXISTS $backupTable LIKE daftar_tagihan_kontainer_sewa");
    DB::statement("INSERT INTO $backupTable SELECT * FROM daftar_tagihan_kontainer_sewa");
    echo "Backup completed: $backupTable\n";
} catch (\Exception $e) {
    echo "Failed to create backup table: " . $e->getMessage() . "\n";
    // Do not proceed if backup fails
    exit(1);
}

// Query: size = '20' and tarif = 'Harian' (case-insensitive)
$items = DaftarTagihanKontainerSewa::where('size', '20')
    ->whereRaw("LOWER(IFNULL(tarif, '')) = 'harian'")
    ->get();

$total = $items->count();
echo "Found $total records matching size=20 and tarif=Harian\n";

foreach ($items as $item) {
    try {
        // Calculate days from tanggal_awal and tanggal_akhir (inclusive)
        if (empty($item->tanggal_awal) || empty($item->tanggal_akhir)) {
            $skipped++;
            $details[] = [
                'id' => $item->id,
                'reason' => 'tanggal_awal or tanggal_akhir not available'
            ];
            continue;
        }

        $start = \Carbon\Carbon::parse($item->tanggal_awal);
        $end = \Carbon\Carbon::parse($item->tanggal_akhir);
        $masa = $start->diffInDays($end) + 1; // +1 untuk inclusive (hitung kedua tanggal)

        $oldDpp = (float) $item->dpp;
        $oldPpn = (float) $item->ppn;
        $oldPph = (float) $item->pph;
        $oldGrand = (float) $item->grand_total;

        // New calculations
        $newDpp = round($dailyRate * $masa, 2);
        $newPpn = round($newDpp * 0.11, 2);
        $newPph = round($newDpp * 0.02, 2);
        $newGrand = round($newDpp + $newPpn - $newPph, 2);

        // Only update if values differ (allow small floating diff)
        $needUpdate = (
            abs($oldDpp - $newDpp) > 0.009 ||
            abs($oldPpn - $newPpn) > 0.009 ||
            abs($oldPph - $newPph) > 0.009 ||
            abs($oldGrand - $newGrand) > 0.009
        );

        if (!$needUpdate) {
            $skipped++;
            continue;
        }

        DB::beginTransaction();

        $item->dpp = $newDpp;
        $item->ppn = $newPpn;
        $item->pph = $newPph;
        $item->grand_total = $newGrand;
        $item->updated_at = now();
        $item->save();

        // If the item belongs to a pranota, recalc pranota totals
        if (!empty($item->pranota_id)) {
            $pranota = PranotaTagihanKontainerSewa::find($item->pranota_id);
            if ($pranota) {
                $tagihanItems = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->get();
                $pranota->total_amount = $tagihanItems->sum('grand_total');
                $pranota->jumlah_tagihan = $tagihanItems->count();
                $pranota->updated_at = now();
                $pranota->save();
            }
        }

        DB::commit();

        $updated++;
        $details[] = [
            'id' => $item->id,
            'old' => ['dpp' => $oldDpp, 'ppn' => $oldPpn, 'pph' => $oldPph, 'grand' => $oldGrand],
            'new' => ['dpp' => $newDpp, 'ppn' => $newPpn, 'pph' => $newPph, 'grand' => $newGrand]
        ];

    } catch (\Exception $e) {
        DB::rollBack();
        $errors++;
        $details[] = ['id' => $item->id, 'error' => $e->getMessage()];
        echo "Error updating id {$item->id}: " . $e->getMessage() . "\n";
    }
}

// Summary
echo "\n=== SUMMARY ===\n";
echo "Total matched: $total\n";
echo "Updated: $updated\n";
echo "Skipped (no change or missing masa): $skipped\n";
echo "Errors: $errors\n";

// Save details to file for audit
$outFile = __DIR__ . '/update_zona20ft_harian_result_' . date('Ymd_His') . '.json';
file_put_contents($outFile, json_encode(['stats' => ['total' => $total, 'updated' => $updated, 'skipped' => $skipped, 'errors' => $errors], 'details' => $details], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Details saved to: $outFile\n";

echo "Done.\n";
