<?php
// scripts/bulatkan_grand_total.php
// Usage: php scripts/bulatkan_grand_total.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;

echo "Starting grand total rounding for all records\n";
echo "Formula: grand_total = round(grand_total)\n\n";

$updated = 0;
$skipped = 0;
$errors = 0;
$details = [];

// Create backup first
$timestamp = date('Ymd_His');
$backupTable = 'daftar_tagihan_kontainer_sewa_backup_bulatkan_' . $timestamp;
try {
    echo "Creating backup table: $backupTable\n";
    DB::statement("CREATE TABLE IF NOT EXISTS $backupTable LIKE daftar_tagihan_kontainer_sewa");
    DB::statement("INSERT INTO $backupTable SELECT * FROM daftar_tagihan_kontainer_sewa");
    echo "Backup completed: $backupTable\n\n";
} catch (\Exception $e) {
    echo "Failed to create backup table: " . $e->getMessage() . "\n";
    exit(1);
}

// Get all records
$items = DaftarTagihanKontainerSewa::all();
$total = $items->count();
echo "Found $total records to process\n\n";

foreach ($items as $item) {
    try {
        $oldGrand = (float) $item->grand_total;
        $newGrand = round($oldGrand);

        // Only update if values differ
        if (abs($oldGrand - $newGrand) > 0.009) {
            DB::beginTransaction();

            // Use raw SQL UPDATE to ensure proper value storage
            DB::statement(
                "UPDATE daftar_tagihan_kontainer_sewa SET grand_total = ?, updated_at = NOW() WHERE id = ?",
                [$newGrand, $item->id]
            );

            // If the item belongs to a pranota, recalc pranota totals
            if (!empty($item->pranota_id)) {
                $tagihanItems = DB::table('daftar_tagihan_kontainer_sewa')
                    ->where('pranota_id', $item->pranota_id)
                    ->get();
                $totalAmount = $tagihanItems->sum('grand_total');
                $jumlahTagihan = $tagihanItems->count();
                
                DB::statement(
                    "UPDATE pranota_tagihan_kontainer_sewa SET total_amount = ?, jumlah_tagihan = ?, updated_at = NOW() WHERE id = ?",
                    [$totalAmount, $jumlahTagihan, $item->pranota_id]
                );
            }

            DB::commit();

            $updated++;
            $details[] = [
                'id' => $item->id,
                'nomor_kontainer' => $item->nomor_kontainer,
                'periode' => $item->periode,
                'old_grand_total' => $oldGrand,
                'new_grand_total' => $newGrand,
                'difference' => $newGrand - $oldGrand
            ];

            // Show progress every 50 records
            if ($updated % 50 == 0) {
                echo "Progress: $updated records updated...\n";
            }
        } else {
            $skipped++;
        }

    } catch (\Exception $e) {
        DB::rollBack();
        $errors++;
        $details[] = ['id' => $item->id, 'error' => $e->getMessage()];
        echo "Error updating id {$item->id}: " . $e->getMessage() . "\n";
    }
}

// Summary
echo "\n=== SUMMARY ===\n";
echo "Total records: $total\n";
echo "Updated: $updated\n";
echo "Skipped (already rounded): $skipped\n";
echo "Errors: $errors\n";

// Calculate total difference
$totalDifference = array_reduce($details, function($carry, $item) {
    return $carry + ($item['difference'] ?? 0);
}, 0);

echo "Total difference: Rp " . number_format($totalDifference, 2, ',', '.') . "\n";

// Save details to file for audit
$outFile = __DIR__ . '/bulatkan_grand_total_result_' . date('Ymd_His') . '.json';
file_put_contents($outFile, json_encode([
    'stats' => [
        'total' => $total,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors' => $errors,
        'total_difference' => $totalDifference
    ],
    'details' => $details
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\nDetails saved to: $outFile\n";

// Show some samples
if (count($details) > 0 && !isset($details[0]['error'])) {
    echo "\n=== SAMPLE CHANGES (First 5) ===\n";
    $samples = array_slice(array_filter($details, function($d) { return !isset($d['error']); }), 0, 5);
    foreach ($samples as $sample) {
        echo sprintf(
            "ID: %s | %s P%s | Old: Rp %s -> New: Rp %s (diff: %+.2f)\n",
            $sample['id'],
            $sample['nomor_kontainer'],
            $sample['periode'],
            number_format($sample['old_grand_total'], 2, ',', '.'),
            number_format($sample['new_grand_total'], 0, ',', '.'),
            $sample['difference']
        );
    }
}

echo "\nDone.\n";
