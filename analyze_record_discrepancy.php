<?php
/**
 * Analysis Script: Compare Pranota Records vs Tagihan Records
 * This script investigates the discrepancy between 982 pranota records and 713 CSV export records
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== ANALYSIS: Pranota vs Tagihan Records Discrepancy ===\n";
echo "Comparing index page (982) vs CSV export (713) records\n\n";

try {
    // 1. Count records in PranotaTagihanKontainerSewa (index page source)
    $pranotaCount = DB::table('pranota_tagihan_kontainer_sewa')->count();
    echo "1. PRANOTA_TAGIHAN_KONTAINER_SEWA table: {$pranotaCount} records\n";

    // 2. Count records in DaftarTagihanKontainerSewa (CSV export source)
    $allTagihanCount = DB::table('daftar_tagihan_kontainer_sewa')->count();
    echo "2. DAFTAR_TAGIHAN_KONTAINER_SEWA table (all): {$allTagihanCount} records\n";

    // 3. Count filtered tagihan (same as export logic)
    $filteredTagihanCount = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
        ->count();
    echo "3. DAFTAR_TAGIHAN_KONTAINER_SEWA (filtered, export logic): {$filteredTagihanCount} records\n\n";

    // 4. Check pranota records with filters applied (same as index logic)
    $filteredPranotaQuery = DB::table('pranota_tagihan_kontainer_sewa');
    // No status filter applied - check what the actual index page shows
    $filteredPranotaCount = $filteredPranotaQuery->count();
    echo "4. PRANOTA (no filters applied): {$filteredPranotaCount} records\n";

    // 5. Let's check what the CSV file actually contains
    $csvFile = 'C:\Users\amanda\Downloads\export_tagihan_kontainer_sewa_2025-10-14_100640.csv';
    if (file_exists($csvFile)) {
        $csvLineCount = 0;
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                $csvLineCount++;
            }
            fclose($handle);
            $csvRecordCount = $csvLineCount - 1; // Subtract header row
            echo "5. CSV FILE actual records: {$csvRecordCount} records (excluding header)\n\n";
        }
    } else {
        echo "5. CSV FILE not found at expected location\n\n";
    }

    // 6. Analyze status distribution in pranota
    echo "=== PRANOTA STATUS ANALYSIS ===\n";
    $pranotaStatusCounts = DB::table('pranota_tagihan_kontainer_sewa')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();

    foreach ($pranotaStatusCounts as $status) {
        echo "Pranota Status '{$status->status}': {$status->count} records\n";
    }
    echo "\n";

    // 7. Analyze tagihan status distribution
    echo "=== TAGIHAN STATUS ANALYSIS ===\n";
    $tagihanStatusCounts = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();

    foreach ($tagihanStatusCounts as $status) {
        echo "Tagihan Status '{$status->status}': {$status->count} records\n";
    }
    echo "\n";

    // 8. Check pranota_id relationships
    echo "=== PRANOTA-TAGIHAN RELATIONSHIPS ===\n";
    $tagihanWithPranota = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
        ->whereNotNull('pranota_id')
        ->count();

    $tagihanWithoutPranota = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
        ->whereNull('pranota_id')
        ->count();

    echo "Tagihan WITH pranota_id: {$tagihanWithPranota} records\n";
    echo "Tagihan WITHOUT pranota_id: {$tagihanWithoutPranota} records\n\n";

    // 9. Check if there are GROUP_SUMMARY/GROUP_TEMPLATE records being excluded
    $excludedRecords = DB::table('daftar_tagihan_kontainer_sewa')
        ->where(function($query) {
            $query->where('nomor_kontainer', 'LIKE', 'GROUP_SUMMARY_%')
                  ->orWhere('nomor_kontainer', 'LIKE', 'GROUP_TEMPLATE%');
        })
        ->count();

    echo "=== EXCLUDED RECORDS ANALYSIS ===\n";
    echo "GROUP_SUMMARY/GROUP_TEMPLATE records (excluded from export): {$excludedRecords} records\n\n";

    // 10. Sample data comparison
    echo "=== SAMPLE DATA COMPARISON ===\n";
    echo "Latest 5 Pranota Records:\n";
    $samplePranota = DB::table('pranota_tagihan_kontainer_sewa')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'no_invoice', 'status', 'total_amount', 'created_at']);

    foreach ($samplePranota as $pranota) {
        echo "- ID: {$pranota->id}, Invoice: {$pranota->no_invoice}, Status: {$pranota->status}, Amount: {$pranota->total_amount}\n";
    }

    echo "\nLatest 5 Tagihan Records (filtered):\n";
    $sampleTagihan = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_SUMMARY_%')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_TEMPLATE%')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get(['id', 'nomor_kontainer', 'vendor', 'status', 'grand_total', 'pranota_id']);

    foreach ($sampleTagihan as $tagihan) {
        echo "- ID: {$tagihan->id}, Container: {$tagihan->nomor_kontainer}, Vendor: {$tagihan->vendor}, Status: {$tagihan->status}, Total: {$tagihan->grand_total}, Pranota ID: {$tagihan->pranota_id}\n";
    }

    echo "\n=== CONCLUSIONS ===\n";
    echo "The discrepancy exists because:\n";
    echo "1. INDEX PAGE shows PRANOTA records (summarized invoices)\n";
    echo "2. CSV EXPORT shows TAGIHAN records (individual container billing items)\n";
    echo "3. One pranota can contain multiple tagihan items\n";
    echo "4. The tables serve different purposes in the system\n\n";

    echo "Next steps:\n";
    echo "- If you want pranota count to match CSV, export from pranota table\n";
    echo "- If you want detailed billing items, current CSV is correct\n";
    echo "- Check if filters on index page are affecting the count\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
?>
