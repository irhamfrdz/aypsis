<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\DaftarTagihanKontainerSewaController;
use App\Models\DaftarTagihanKontainerSewa;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Fix for BOM Keys ===\n";

$csvFile = 'C:\\Users\\amanda\\Downloads\\Tagihan Kontainer Sewa DPE.csv';

if (!file_exists($csvFile)) {
    echo "CSV file not found!\n";
    exit(1);
}

$handle = fopen($csvFile, 'r');
$delimiter = ';';
$headers = [];
$rowNumber = 0;

while (($row = fgetcsv($handle, 1000, $delimiter)) !== false && $rowNumber < 3) {
    $rowNumber++;

    if ($rowNumber === 1) {
        $headers = array_map('trim', $row);
        // Clean BOM from first header
        if (!empty($headers[0])) {
            $headers[0] = str_replace("\xEF\xBB\xBF", "", $headers[0]);
            $headers[0] = preg_replace('/^\x{FEFF}/u', '', $headers[0]);
        }

        echo "Headers after cleaning: " . json_encode($headers) . "\n";
        continue;
    }

    // Map row data
    $data = [];
    foreach ($headers as $index => $header) {
        $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
    }

    echo "\nRow $rowNumber data:\n";
    echo "Keys available: " . json_encode(array_keys($data)) . "\n";
    echo "Group value: '" . ($data['Group'] ?? 'NOT FOUND') . "'\n";
    echo "Kontainer value: '" . ($data['Kontainer'] ?? 'NOT FOUND') . "'\n";

    // Test detection
    $isDpeFormat = in_array('Group', $headers) && in_array('Kontainer', $headers);
    echo "DPE format detected: " . ($isDpeFormat ? 'YES' : 'NO') . "\n";
}

fclose($handle);

echo "\n=== Fix Test Complete ===\n";
