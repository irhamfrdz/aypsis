<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== TAGIHAN KONTAINER SEWA PERFORMANCE ANALYSIS ===\n\n";

// 1. Count total records
$totalRecords = DaftarTagihanKontainerSewa::count();
echo "Total Records: " . number_format($totalRecords) . "\n";

// 2. Count by vendor
echo "\n--- Records by Vendor ---\n";
$vendorCounts = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('vendor', DB::raw('COUNT(*) as count'))
    ->groupBy('vendor')
    ->orderBy('count', 'desc')
    ->get();

foreach($vendorCounts as $vc) {
    echo "- {$vc->vendor}: " . number_format($vc->count) . "\n";
}

// 3. Count by periode
echo "\n--- Records by Periode (Top 10) ---\n";
$periodeCounts = DB::table('daftar_tagihan_kontainer_sewa')
    ->select('periode', DB::raw('COUNT(*) as count'))
    ->groupBy('periode')
    ->orderBy('count', 'desc')
    ->limit(10)
    ->get();

foreach($periodeCounts as $pc) {
    echo "- Periode {$pc->periode}: " . number_format($pc->count) . "\n";
}

// 4. Test query performance
echo "\n--- Query Performance Test ---\n";

$start = microtime(true);
$testQuery1 = DaftarTagihanKontainerSewa::where('vendor', 'DPE')->count();
$time1 = (microtime(true) - $start) * 1000;
echo "Query vendor='DPE': {$testQuery1} records in " . number_format($time1, 2) . "ms\n";

$start = microtime(true);
$testQuery2 = DaftarTagihanKontainerSewa::where('periode', 1)->count();
$time2 = (microtime(true) - $start) * 1000;
echo "Query periode=1: {$testQuery2} records in " . number_format($time2, 2) . "ms\n";

$start = microtime(true);
$testQuery3 = DaftarTagihanKontainerSewa::where('vendor', 'DPE')
    ->where('periode', 1)
    ->count();
$time3 = (microtime(true) - $start) * 1000;
echo "Query vendor='DPE' AND periode=1: {$testQuery3} records in " . number_format($time3, 2) . "ms\n";

// 5. Full pagination test
echo "\n--- Pagination Performance Test ---\n";
$start = microtime(true);
$paginated = DaftarTagihanKontainerSewa::orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->paginate(25);
$time4 = (microtime(true) - $start) * 1000;
echo "Paginated query (25 per page): " . $paginated->count() . " records in " . number_format($time4, 2) . "ms\n";

// 6. Check indexes
echo "\n--- Database Indexes ---\n";
try {
    $indexes = DB::select("SHOW INDEX FROM daftar_tagihan_kontainer_sewa");
    $indexNames = array_unique(array_column($indexes, 'Key_name'));
    foreach($indexNames as $indexName) {
        if ($indexName !== 'PRIMARY') {
            echo "- Index: {$indexName}\n";
        }
    }
} catch (Exception $e) {
    echo "Could not check indexes: " . $e->getMessage() . "\n";
}

echo "\n=== PERFORMANCE RECOMMENDATIONS ===\n";

if ($totalRecords > 10000) {
    echo "• Large dataset detected (" . number_format($totalRecords) . " records)\n";
    echo "• Consider adding more specific filters on index page\n";
    echo "• Pagination is working efficiently\n";
}

if ($time1 > 100) {
    echo "• Vendor query is slow (" . number_format($time1, 2) . "ms) - check vendor index\n";
} else {
    echo "• Vendor queries are fast (" . number_format($time1, 2) . "ms) ✓\n";
}

if ($time4 > 500) {
    echo "• Pagination query is slow (" . number_format($time4, 2) . "ms) - consider reducing page size\n";
} else {
    echo "• Pagination performance is good (" . number_format($time4, 2) . "ms) ✓\n";
}

echo "\n=== CACHE SUGGESTIONS ===\n";
echo "• Filter options are now cached for 5 minutes\n";
echo "• Run 'php artisan tagihan:clear-cache' to refresh filter cache\n";
echo "• Consider adding Redis cache for high-traffic sites\n";

echo "\nAnalysis completed!\n";
