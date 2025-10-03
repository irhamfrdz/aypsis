<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "Testing import with financial calculations...\n\n";

// Truncate table
echo "Truncating table...\n";
DB::table('daftar_tagihan_kontainer_sewa')->truncate();

// Import CSV
$csvPath = 'C:\folder_kerjaan\aypsis\test_csv_with_financial.csv';

if (!file_exists($csvPath)) {
    die("Error: CSV file not found at {$csvPath}\n");
}

echo "Importing CSV: {$csvPath}\n";

$controller = new \App\Http\Controllers\DaftarTagihanKontainerSewaController();

$file = new \Illuminate\Http\UploadedFile(
    $csvPath,
    'test.csv',
    'text/csv',
    null,
    true
);

$options = [
    'validate_only' => false,
    'skip_duplicates' => true,
    'update_existing' => false,
];

$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('processCsvImport');
$method->setAccessible(true);

try {
    $results = $method->invoke($controller, $file, $options);

    echo "\nImport Results:\n";
    echo "  Success: " . ($results['success'] ? 'YES' : 'NO') . "\n";
    echo "  Imported: {$results['imported_count']}\n";
    echo "  Errors: " . count($results['errors']) . "\n";

    if (!empty($results['errors'])) {
        echo "\nErrors:\n";
        foreach (array_slice($results['errors'], 0, 5) as $error) {
            echo "  Row {$error['row']}: {$error['message']}\n";
        }
    }

    // Check specific container
    echo "\n\nChecking CBHU3952697 financial data:\n";
    $tagihans = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'CBHU3952697')
        ->orderBy('periode')
        ->get();

    foreach ($tagihans as $tagihan) {
        echo "\nPeriode {$tagihan->periode}:\n";
        echo "  DPP: " . number_format($tagihan->dpp, 2) . "\n";
        echo "  Adjustment: " . number_format($tagihan->adjustment, 2) . "\n";
        echo "  DPP Nilai Lain: " . number_format($tagihan->dpp_nilai_lain, 2) . "\n";
        echo "  PPN (12%): " . number_format($tagihan->ppn, 2) . "\n";
        echo "  PPH (2%): " . number_format($tagihan->pph, 2) . "\n";
        echo "  Grand Total: " . number_format($tagihan->grand_total, 2) . "\n";
        echo "  Tarif: {$tagihan->tarif}\n";

        // Verify calculation
        $expectedPpn = round($tagihan->dpp * 0.12, 2);
        $expectedPph = round($tagihan->dpp * 0.02, 2);
        $expectedGrandTotal = round($tagihan->dpp + $expectedPpn - $expectedPph, 2);

        $ppnMatch = abs($tagihan->ppn - $expectedPpn) < 0.01 ? '✓' : '✗';
        $pphMatch = abs($tagihan->pph - $expectedPph) < 0.01 ? '✓' : '✗';
        $grandTotalMatch = abs($tagihan->grand_total - $expectedGrandTotal) < 0.01 ? '✓' : '✗';

        echo "  Verification: PPN {$ppnMatch}, PPH {$pphMatch}, Grand Total {$grandTotalMatch}\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
