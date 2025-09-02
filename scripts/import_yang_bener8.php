<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "Starting import process for yang_bener8.csv...\n";

// Check current record count
$currentCount = DaftarTagihanKontainerSewa::count();
echo "Current records in database: {$currentCount}\n";

$csvFile = __DIR__ . '/../yang bener8.csv';

if (!file_exists($csvFile)) {
    echo "Error: CSV file not found at {$csvFile}\n";
    exit(1);
}

echo "Reading CSV file: {$csvFile}\n";

$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "Error: Cannot open CSV file\n";
    exit(1);
}

// Skip header row
$header = fgetcsv($handle, 0, ';');
echo "CSV Headers: " . implode(', ', $header) . "\n";

$data = [];
$lineNumber = 2; // Start from line 2 (after header)

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    if (count($row) >= 16) { // Ensure we have all required columns
        // Handle empty dates
        $tanggalAwal = !empty($row[4]) ? $row[4] : null;
        $tanggalAkhir = !empty($row[5]) ? $row[5] : null;

        $data[] = [
            'vendor' => $row[0],
            'nomor_kontainer' => $row[1],
            'size' => intval($row[2]),
            'group' => $row[3],
            'tanggal_awal' => $tanggalAwal,
            'tanggal_akhir' => $tanggalAkhir,
            'periode' => intval($row[6]),
            'masa' => $row[7],
            'tarif' => $row[8],
            'tarif_nominal' => floatval(str_replace(',', '.', $row[9])), // This maps to 'dpp' column in CSV
            'dpp' => floatval(str_replace(',', '.', $row[9])),
            'dpp_nilai_lain' => floatval(str_replace(',', '.', $row[10])),
            'ppn' => floatval(str_replace(',', '.', $row[11])),
            'pph' => floatval(str_replace(',', '.', $row[12])),
            'grand_total' => floatval(str_replace(',', '.', $row[13])),
            'status' => $row[14] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    $lineNumber++;
}

fclose($handle);

echo "Total rows to import: " . count($data) . "\n";

if (empty($data)) {
    echo "No data to import\n";
    exit(0);
}

// Import in chunks to avoid memory issues
$chunkSize = 100;
$chunks = array_chunk($data, $chunkSize);
$totalImported = 0;

DB::beginTransaction();
try {
    foreach ($chunks as $index => $chunk) {
        DaftarTagihanKontainerSewa::insert($chunk);
        $totalImported += count($chunk);
        echo "Imported chunk " . ($index + 1) . "/" . count($chunks) . " ({$totalImported} total records)\n";
    }

    DB::commit();
    echo "\nImport completed successfully!\n";
    echo "Total records imported: {$totalImported}\n";

    // Show final count
    $finalCount = DaftarTagihanKontainerSewa::count();
    echo "Final records in database: {$finalCount}\n";

    // Show group summary
    $groups = DaftarTagihanKontainerSewa::select('group')
        ->groupBy('group')
        ->orderBy('group')
        ->pluck('group');

    echo "\nGroups imported:\n";
    foreach ($groups as $group) {
        $count = DaftarTagihanKontainerSewa::where('group', $group)->count();
        echo "- {$group}: {$count} records\n";
    }

} catch (Exception $e) {
    DB::rollback();
    echo "Error during import: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone!\n";
