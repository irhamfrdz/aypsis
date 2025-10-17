<?php
/**
 * Debug Pranota Surat Jalan Query
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuratJalan;
use Illuminate\Support\Facades\DB;

echo "=== Debug Pranota Surat Jalan Query ===\n\n";

// 1. Check all surat jalan
echo "1. Total Surat Jalan in Database:\n";
$totalSJ = SuratJalan::count();
echo "   Total: {$totalSJ}\n\n";

// 2. Check surat jalan with approvals
echo "2. Surat Jalan dengan Approvals:\n";
$suratJalansWithApprovals = SuratJalan::has('approvals')->get();
echo "   Total: {$suratJalansWithApprovals->count()}\n\n";

// 3. Check each surat jalan status
echo "3. Detail Approval Status:\n";
foreach ($suratJalansWithApprovals as $sj) {
    echo "\n   No. SJ: {$sj->no_surat_jalan}\n";
    echo "   Status: {$sj->status}\n";

    foreach ($sj->approvals as $approval) {
        echo "   - Level: {$approval->approval_level}, Status: {$approval->status}\n";
    }
}

// 4. Test current query (from controller)
echo "\n4. Current Query Result (tugas-1 + tugas-2):\n";
$currentQuery = SuratJalan::whereHas('approvals', function($query) {
    $query->where('approval_level', 'tugas-1')->where('status', 'approved');
})
->whereHas('approvals', function($query) {
    $query->where('approval_level', 'tugas-2')->where('status', 'approved');
})
->whereDoesntHave('pranotaSuratJalan')
->get();

echo "   Found: {$currentQuery->count()} surat jalan\n";
foreach ($currentQuery as $sj) {
    echo "   - {$sj->no_surat_jalan}\n";
}

// 5. Check which surat jalan have approval level
echo "\n5. Check Approval Levels Available:\n";
$approvalLevels = DB::table('surat_jalan_approvals')
    ->distinct()
    ->pluck('approval_level')
    ->toArray();
echo "   Levels: " . implode(', ', $approvalLevels) . "\n";

// 6. Check surat jalan status column
echo "\n6. Surat Jalan Status Values:\n";
$statuses = DB::table('surat_jalans')
    ->distinct()
    ->pluck('status')
    ->toArray();
echo "   Statuses: " . implode(', ', $statuses) . "\n";

// 7. Check which status means "fully approved"
echo "\n7. Surat Jalan by Status:\n";
foreach ($statuses as $status) {
    $count = SuratJalan::where('status', $status)->count();
    echo "   - {$status}: {$count}\n";
}

// 8. Try alternative query - use status column directly
echo "\n8. Alternative Query (using status = 'fully_approved' or 'approved'):\n";
$alternativeQuery = SuratJalan::where('status', 'fully_approved')
    ->orWhere('status', 'approved')
    ->whereDoesntHave('pranotaSuratJalan')
    ->get();

echo "   Found: {$alternativeQuery->count()} surat jalan\n";
foreach ($alternativeQuery as $sj) {
    echo "   - {$sj->no_surat_jalan} (status: {$sj->status})\n";
}

// 9. Check pranota table
echo "\n9. Existing Pranota:\n";
$pranotaCount = DB::table('pranota_surat_jalans')->count();
echo "   Total Pranota: {$pranotaCount}\n";

$pranotaWithSJ = DB::table('pranota_surat_jalans')
    ->pluck('surat_jalan_id')
    ->toArray();
echo "   Surat Jalan IDs already in Pranota: " . implode(', ', $pranotaWithSJ) . "\n";

// 10. Recommendation
echo "\n=== RECOMMENDATION ===\n";
if ($currentQuery->count() === 0) {
    echo "❌ Current query returns 0 results\n";
    echo "\nPossible issues:\n";
    echo "1. Check if approval status is 'approved' or something else\n";
    echo "2. Check if approval_level names match ('tugas-1', 'tugas-2')\n";
    echo "3. Check if surat jalan status is set correctly\n";
    echo "\nSuggested fix: Use status column directly instead of approval relationship\n";
    echo "Query: SuratJalan::where('status', 'fully_approved')\n";
} else {
    echo "✓ Query working correctly, found {$currentQuery->count()} surat jalan\n";
}
