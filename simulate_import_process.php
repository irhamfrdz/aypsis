<?php
/**
 * Simulasi proses import persis seperti di controller
 */

echo "=== SIMULASI PROSES IMPORT ===\n\n";

// Fungsi helper seperti di controller
function cleanDpeNumber($value) {
    if (empty($value) || trim($value) === '' || trim($value) === '-') {
        return 0;
    }

    // Handle negative values with comma format
    $value = trim($value);
    $isNegative = false;

    if (strpos($value, '-') !== false) {
        $isNegative = true;
        $value = str_replace('-', '', $value);
    }

    // Remove currency symbols, spaces, and formatting
    $cleaned = preg_replace('/[^\d.,]/', '', $value);
    $cleaned = str_replace(',', '', $cleaned); // Remove thousands separator

    $result = (float) $cleaned;
    return $isNegative ? -$result : $result;
}

function getValue($row, $headers, $columnName) {
    $index = array_search($columnName, $headers);
    return $index !== false && isset($row[$index]) ? $row[$index] : null;
}

$csvFile = 'Zona_SIAP_IMPORT_FINAL_TARIF_BENAR_COMMA.csv';

if (!file_exists($csvFile)) {
    echo "File tidak ditemukan: $csvFile\n";
    exit(1);
}

// Header mapping seperti di controller
$headerMapping = [
    'Vendor' => 'vendor',
    'Nomor Kontainer' => 'nomor_kontainer',
    'Size' => 'size',
    'Tanggal Awal' => 'tanggal_awal',
    'Tanggal Akhir' => 'tanggal_akhir',
    'Tarif' => 'tarif',
    'Adjustment' => 'adjustment',
    'Periode' => 'periode',
    'Group' => 'group',
    'Status' => 'status'
];

// Baca file CSV
$handle = fopen($csvFile, 'r');
$headers = fgetcsv($handle, 1000, ',');

echo "Headers CSV: " . implode(' | ', $headers) . "\n\n";

// Process some rows
$rowCount = 0;
echo "Proses data row by row:\n";

while (($row = fgetcsv($handle, 1000, ',')) !== false && $rowCount < 10) {
    $rowCount++;

    // Clean data seperti di controller
    $cleanData = [];

    foreach ($headerMapping as $csvHeader => $dbField) {
        $value = getValue($row, $headers, $csvHeader);

        if ($dbField === 'adjustment') {
            $cleanData[$dbField] = cleanDpeNumber($value);
            echo "Row $rowCount - Raw adjustment: '$value' -> Clean: {$cleanData[$dbField]}\n";
        } elseif (in_array($dbField, ['vendor', 'nomor_kontainer', 'size', 'tarif', 'periode', 'group', 'status'])) {
            $cleanData[$dbField] = $value;
        } elseif (in_array($dbField, ['tanggal_awal', 'tanggal_akhir'])) {
            // Convert date format
            if (!empty($value)) {
                try {
                    $cleanData[$dbField] = date('Y-m-d', strtotime($value));
                } catch (Exception $e) {
                    $cleanData[$dbField] = null;
                }
            } else {
                $cleanData[$dbField] = null;
            }
        }
    }

    echo "Row $rowCount data:\n";
    echo "  Kontainer: " . ($cleanData['nomor_kontainer'] ?? 'N/A') . "\n";
    echo "  Adjustment: " . ($cleanData['adjustment'] ?? 'N/A') . "\n";
    echo "  Tarif: " . ($cleanData['tarif'] ?? 'N/A') . "\n\n";
}

fclose($handle);

echo "=== KESIMPULAN ===\n";
echo "Proses parsing terlihat normal. Adjustment values terbaca dengan benar.\n";
echo "Masalah mungkin ada di:\n";
echo "1. Validasi model yang menolak nilai adjustment\n";
echo "2. Mass assignment yang tidak mengizinkan field adjustment\n";
echo "3. Database constraint atau trigger\n";
echo "4. Proses save yang di-override\n";
