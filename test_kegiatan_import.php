<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MasterKegiatan;

echo "Testing CSV Import for Master Kegiatan...\n\n";

$csvPath = 'C:\folder_kerjaan\aypsis\test_import_kegiatan.csv';

if (!file_exists($csvPath)) {
    die("Error: CSV file not found at {$csvPath}\n");
}

$handle = fopen($csvPath, 'r');
if ($handle === false) {
    die("Error: Cannot open CSV file\n");
}

// Detect delimiter
$firstLine = fgets($handle);
rewind($handle);
$delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

echo "Detected delimiter: " . ($delimiter === ';' ? 'semicolon' : 'comma') . "\n\n";

// Read header
$header = fgetcsv($handle, 0, $delimiter);

// Remove BOM from first header if present
if (!empty($header[0])) {
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
    $header[0] = preg_replace('/^[\x{FEFF}]/u', '', $header[0]);
}

echo "Headers: " . implode(', ', $header) . "\n\n";

// Normalize headers
$norm = array_map(function($v){ return strtolower(trim($v)); }, (array)$header);
echo "Normalized headers: " . implode(', ', $norm) . "\n\n";

$created = 0;
$errors = [];
$line = 1;

echo "Processing rows...\n";

while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
    $line++;
    $row = array_map('trim', $row);

    // skip empty rows
    if (count($row) < 2 || (empty($row[0]) && empty($row[1]))) {
        echo "  Row {$line}: Skipped (empty)\n";
        continue;
    }

    // Map row to associative array
    $data = [];
    foreach ($norm as $index => $headerName) {
        $data[$headerName] = $row[$index] ?? '';
    }

    echo "  Row {$line}: kode={$data['kode_kegiatan']}, nama={$data['nama_kegiatan']}, status={$data['status']}\n";

    // validate minimal fields
    if (empty($data['kode_kegiatan']) || empty($data['nama_kegiatan'])) {
        $errors[] = "Baris {$line}: kode_kegiatan dan nama_kegiatan wajib.";
        echo "    ERROR: Minimal fields required\n";
        continue;
    }

    // Check if kode already exists
    $exists = MasterKegiatan::where('kode_kegiatan', $data['kode_kegiatan'])->exists();
    if ($exists) {
        $errors[] = "Baris {$line}: kode_kegiatan {$data['kode_kegiatan']} sudah ada, dilewati.";
        echo "    SKIPPED: Already exists\n";
        continue;
    }

    // ensure status valid
    $status = strtolower($data['status'] ?? '');
    if (!in_array($status, ['aktif','nonaktif'])) {
        $status = 'aktif';
    }

    try {
        MasterKegiatan::create([
            'kode_kegiatan' => $data['kode_kegiatan'],
            'nama_kegiatan' => $data['nama_kegiatan'],
            'keterangan' => $data['keterangan'] ?? null,
            'status' => $status,
        ]);
        $created++;
        echo "    SUCCESS: Created\n";
    } catch (\Exception $e) {
        $errors[] = "Baris {$line}: " . $e->getMessage();
        echo "    ERROR: " . $e->getMessage() . "\n";
    }
}

fclose($handle);

echo "\n";
echo "====================================\n";
echo "Import Summary:\n";
echo "  Successfully created: {$created}\n";
echo "  Errors: " . count($errors) . "\n";
echo "====================================\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
}

// Verify created data
echo "\n\nVerifying created data:\n";
$testRecords = MasterKegiatan::where('kode_kegiatan', 'LIKE', 'KGT-TEST-%')->get();
foreach ($testRecords as $record) {
    echo "  ✓ {$record->kode_kegiatan} - {$record->nama_kegiatan} - {$record->status}\n";
}
