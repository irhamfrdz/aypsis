<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Test Import CSV ===\n\n";

// Path ke file CSV
$csvPath = 'C:/Users/amanda/Downloads/template_import_dpe_auto_group.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: File tidak ditemukan: $csvPath\n";
    exit(1);
}

echo "File ditemukan: $csvPath\n";
echo "Ukuran file: " . filesize($csvPath) . " bytes\n\n";

// Baca file CSV
$handle = fopen($csvPath, 'r');
if (!$handle) {
    echo "ERROR: Tidak dapat membaca file\n";
    exit(1);
}

// Detect delimiter
$firstLine = fgets($handle);
rewind($handle);
$delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

echo "Delimiter terdeteksi: '$delimiter'\n\n";

// Baca header
$headers = fgetcsv($handle, 1000, $delimiter);
if (!$headers) {
    echo "ERROR: Tidak dapat membaca header\n";
    exit(1);
}

// Clean headers dari BOM
$headers = array_map(function($header) {
    $cleaned = str_replace("\xEF\xBB\xBF", "", $header);
    $cleaned = preg_replace('/^\x{FEFF}/u', '', $cleaned);
    return trim($cleaned);
}, $headers);

echo "Headers yang ditemukan:\n";
print_r($headers);
echo "\n";

// Baca beberapa baris data
$rowCount = 0;
$sampleRows = [];

while (($row = fgetcsv($handle, 1000, $delimiter)) !== false && $rowCount < 5) {
    if (!empty(array_filter($row))) {
        $rowCount++;

        // Map row to data
        $data = [];
        foreach ($headers as $index => $header) {
            $value = isset($row[$index]) ? trim($row[$index]) : '';
            $value = str_replace("\xEF\xBB\xBF", "", $value);
            $data[$header] = $value;
        }

        $sampleRows[] = $data;
    }
}

fclose($handle);

echo "Sample data ($rowCount baris):\n";
foreach ($sampleRows as $i => $row) {
    echo "\nBaris " . ($i + 2) . ":\n";
    foreach ($row as $key => $value) {
        echo "  $key: $value\n";
    }
}

// Check if database table exists
echo "\n=== Checking Database ===\n";
try {
    $tableExists = DB::getSchemaBuilder()->hasTable('daftar_tagihan_kontainer_sewa');
    echo "Table 'daftar_tagihan_kontainer_sewa' exists: " . ($tableExists ? 'YES' : 'NO') . "\n";

    if ($tableExists) {
        $count = DB::table('daftar_tagihan_kontainer_sewa')->count();
        echo "Current record count: $count\n";

        // Show columns
        echo "\nTable columns:\n";
        $columns = DB::getSchemaBuilder()->getColumnListing('daftar_tagihan_kontainer_sewa');
        print_r($columns);
    }
} catch (\Exception $e) {
    echo "ERROR checking database: " . $e->getMessage() . "\n";
}

echo "\n=== Test selesai ===\n";
