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

echo "=== TESTING PERIODE FROM CSV ===\n\n";

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
    echo "  Updated: " . ($data['updated_count'] ?? 0) . "\n";
    echo "  Skipped: " . ($data['skipped_count'] ?? 0) . "\n";
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

// Check imported data - focus on periode
echo "Checking imported data (first 10 records):\n";
echo str_repeat('-', 120) . "\n";
printf("%-20s | %-8s | %-15s | %-15s | %-8s | %-10s\n",
    "Kontainer", "Size", "Tanggal Awal", "Tanggal Akhir", "Periode", "Masa");
echo str_repeat('-', 120) . "\n";

$records = DaftarTagihanKontainerSewa::orderBy('nomor_kontainer')->orderBy('periode')->take(10)->get();

foreach ($records as $record) {
    printf("%-20s | %-8s | %-15s | %-15s | %-8s | %-10s\n",
        substr($record->nomor_kontainer, 0, 20),
        $record->size,
        $record->tanggal_awal,
        $record->tanggal_akhir,
        $record->periode,
        $record->masa
    );
}

echo str_repeat('-', 120) . "\n\n";

// Check specific container with multiple periods
echo "Checking CBHU3952697 (should have periods 1, 2, 3):\n";
$cbhu3952697 = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'CBHU3952697')
    ->orderBy('periode')
    ->get();

if ($cbhu3952697->isEmpty()) {
    echo "  ❌ Container not found!\n";
} else {
    echo "  ✓ Found " . $cbhu3952697->count() . " records\n";
    foreach ($cbhu3952697 as $record) {
        echo "    Periode: {$record->periode} | Masa: {$record->masa} | ";
        echo "Tanggal: {$record->tanggal_awal} s/d {$record->tanggal_akhir}\n";
    }
}

echo "\n=== END TEST ===\n";
