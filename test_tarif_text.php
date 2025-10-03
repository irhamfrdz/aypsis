<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\DaftarTagihanKontainerSewaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== TESTING TARIF AS TEXT (Bulanan/Harian) ===\n\n";

// Login as admin
Auth::loginUsingId(1);

// Clear existing data first
DB::table('daftar_tagihan_kontainer_sewa')->truncate();
echo "Cleared existing data\n\n";

// Use the CSV file from Downloads
$csvPath = 'C:\\Users\\amanda\\Downloads\\export_tagihan_kontainer_sewa_2025-10-02_153813.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: File not found: {$csvPath}\n";
    exit(1);
}

// Create a fake UploadedFile
$uploadedFile = new UploadedFile(
    $csvPath,
    'export_tagihan_kontainer_sewa_2025-10-02_153813.csv',
    'text/csv',
    null,
    true // test mode
);

echo "Processing import...\n";
$controller = new DaftarTagihanKontainerSewaController();

$request = Request::create('/import/process', 'POST', [
    'validate_only' => '0',  // Actually save data
    'skip_duplicates' => '1',
    'update_existing' => '0',
]);
$request->headers->set('Accept', 'application/json');
$request->files->set('import_file', $uploadedFile);

try {
    $response = $controller->processImport($request);
    $data = $response->getData(true);

    echo "Import Results:\n";
    echo "  Success: " . ($data['success'] ? 'YES' : 'NO') . "\n";
    echo "  Imported: " . ($data['imported_count'] ?? 0) . "\n";
    echo "  Errors: " . count($data['errors'] ?? []) . "\n\n";

    if (!empty($data['errors'])) {
        echo "First 3 errors:\n";
        foreach (array_slice($data['errors'], 0, 3) as $error) {
            echo "  Row {$error['row']}: {$error['message']}\n";
        }
        echo "\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

// Check imported data - focus on tarif field
echo "Checking TARIF field (should be 'Bulanan' or 'Harian' text):\n";
echo str_repeat('-', 100) . "\n";
printf("%-20s | %-8s | %-15s | %-15s | %-8s | %-10s\n",
    "Kontainer", "Periode", "Tarif", "DPP", "Grand Total", "Masa");
echo str_repeat('-', 100) . "\n";

$records = DaftarTagihanKontainerSewa::orderBy('nomor_kontainer')->orderBy('periode')->take(15)->get();

foreach ($records as $record) {
    printf("%-20s | %-8s | %-15s | %-15s | %-10s | %-10s\n",
        substr($record->nomor_kontainer, 0, 20),
        $record->periode,
        $record->tarif, // Should be "Bulanan" or "Harian"
        number_format($record->dpp ?? 0, 0, ',', '.'),
        number_format($record->grand_total ?? 0, 0, ',', '.'),
        $record->masa
    );
}

echo str_repeat('-', 100) . "\n\n";

// Check specific examples
echo "Checking specific examples from CSV:\n";

// Row 2 from CSV: CBHU3952697, Periode 1, Tarif = Bulanan
$example1 = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'CBHU3952697')
    ->where('periode', 1)
    ->first();

if ($example1) {
    echo "✓ CBHU3952697 Periode 1:\n";
    echo "  Tarif: '{$example1->tarif}' (expected: 'Bulanan')\n";
    echo "  Type: " . gettype($example1->tarif) . "\n";
    echo "  DPP: " . number_format($example1->dpp, 0, ',', '.') . "\n";
    echo "  Match: " . ($example1->tarif === 'Bulanan' ? '✓ YES' : '✗ NO') . "\n\n";
} else {
    echo "✗ CBHU3952697 Periode 1 not found\n\n";
}

// Row 4 from CSV: CBHU3952697, Periode 3, Tarif = Harian
$example2 = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'CBHU3952697')
    ->where('periode', 3)
    ->first();

if ($example2) {
    echo "✓ CBHU3952697 Periode 3:\n";
    echo "  Tarif: '{$example2->tarif}' (expected: 'Harian')\n";
    echo "  Type: " . gettype($example2->tarif) . "\n";
    echo "  DPP: " . number_format($example2->dpp, 0, ',', '.') . "\n";
    echo "  Match: " . ($example2->tarif === 'Harian' ? '✓ YES' : '✗ NO') . "\n\n";
} else {
    echo "✗ CBHU3952697 Periode 3 not found\n\n";
}

echo "=== END TEST ===\n";
