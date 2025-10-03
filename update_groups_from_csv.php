<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "Updating groups from CSV file...\n\n";

$csvPath = 'C:\Users\amanda\Downloads\Tagihan Kontainer Sewa DPE.csv';

if (!file_exists($csvPath)) {
    die("Error: CSV file not found at {$csvPath}\n");
}

// Read CSV file
$handle = fopen($csvPath, 'r');
if (!$handle) {
    die("Error: Cannot open CSV file\n");
}

// Detect delimiter
$firstLine = fgets($handle);
rewind($handle);
$delimiter = ';'; // CSV menggunakan semicolon

$rowNumber = 0;
$headers = [];
$groupMapping = []; // [kontainer][periode] => group
$updated = 0;
$notFound = 0;
$errors = [];

echo "Reading CSV file...\n";

while (($row = fgetcsv($handle, 10000, $delimiter)) !== false) {
    $rowNumber++;

    try {
        // First row is header
        if ($rowNumber === 1) {
            $headers = array_map('trim', $row);

            // Remove BOM from first header if present
            if (!empty($headers[0])) {
                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
                $headers[0] = preg_replace('/^[\x{FEFF}]/u', '', $headers[0]);
            }

            echo "Headers: " . implode(', ', array_slice($headers, 0, 10)) . "\n\n";
            continue;
        }

        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }

        // Map row data to associative array
        $data = [];
        foreach ($headers as $index => $header) {
            $value = isset($row[$index]) ? trim($row[$index]) : '';
            $data[$header] = $value;
        }

        // Get values from CSV
        $group = trim($data['Group'] ?? '');
        $kontainer = strtoupper(trim($data['Kontainer'] ?? ''));
        $periode = trim($data['Periode'] ?? '');

        // Skip if essential data is missing
        if (empty($kontainer) || empty($periode)) {
            continue;
        }

        // Store mapping: kontainer + periode => group
        if (!isset($groupMapping[$kontainer])) {
            $groupMapping[$kontainer] = [];
        }
        $groupMapping[$kontainer][$periode] = $group;

    } catch (\Exception $e) {
        $errors[] = "Row {$rowNumber}: " . $e->getMessage();
    }
}

fclose($handle);

echo "Found " . count($groupMapping) . " unique containers in CSV\n";
echo "Total mappings: " . array_sum(array_map('count', $groupMapping)) . "\n\n";

// Show sample of mapping
echo "Sample mappings:\n";
$sampleCount = 0;
foreach ($groupMapping as $kontainer => $periodes) {
    foreach ($periodes as $periode => $group) {
        echo "  {$kontainer} Periode {$periode} => Group {$group}\n";
        $sampleCount++;
        if ($sampleCount >= 5) break 2;
    }
}
echo "\n";

// Ask for confirmation
echo "Proceed with updating database? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirmation = trim(strtolower($line));
fclose($handle);

if ($confirmation !== 'yes' && $confirmation !== 'y') {
    die("Update cancelled by user.\n");
}

echo "\nUpdating database...\n\n";

DB::beginTransaction();

try {
    foreach ($groupMapping as $kontainer => $periodes) {
        foreach ($periodes as $periode => $group) {
            // Find record in database
            $record = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer)
                ->where('periode', $periode)
                ->first();

            if ($record) {
                $oldGroup = $record->group;
                $record->group = $group;
                $record->save();

                $updated++;

                if ($updated <= 10) {
                    echo "✓ Updated {$kontainer} Periode {$periode}: Group '{$oldGroup}' => '{$group}'\n";
                }
            } else {
                $notFound++;
                if ($notFound <= 10) {
                    echo "✗ Not found: {$kontainer} Periode {$periode}\n";
                }
            }
        }
    }

    DB::commit();

    echo "\n";
    echo "====================================\n";
    echo "Update Summary:\n";
    echo "  Total mappings in CSV: " . array_sum(array_map('count', $groupMapping)) . "\n";
    echo "  Successfully updated: {$updated}\n";
    echo "  Not found in database: {$notFound}\n";
    echo "  Errors: " . count($errors) . "\n";
    echo "====================================\n\n";

    if (!empty($errors)) {
        echo "Errors:\n";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "  {$error}\n";
        }
    }

    // Show some examples of updated records
    echo "\nVerifying updates (sample):\n";
    $verifyCount = 0;
    foreach ($groupMapping as $kontainer => $periodes) {
        foreach ($periodes as $periode => $group) {
            $record = DaftarTagihanKontainerSewa::where('nomor_kontainer', $kontainer)
                ->where('periode', $periode)
                ->first();

            if ($record) {
                $match = ($record->group == $group) ? '✓' : '✗';
                echo "{$match} {$kontainer} Periode {$periode}: Group = '{$record->group}'\n";
                $verifyCount++;
                if ($verifyCount >= 5) break 2;
            }
        }
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
