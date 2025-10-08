<?php
/**
 * Test script untuk melihat parsing CSV adjustment
 */

echo "=== TEST CSV PARSING ADJUSTMENT ===\n\n";

$csvFile = 'Zona_SIAP_IMPORT_FINAL_TARIF_BENAR_COMMA.csv';

if (!file_exists($csvFile)) {
    echo "File tidak ditemukan: $csvFile\n";
    exit(1);
}

// Baca file CSV
$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "Tidak bisa membuka file\n";
    exit(1);
}

// Read header
$headers = fgetcsv($handle, 1000, ',');
echo "Headers: " . implode(' | ', $headers) . "\n\n";

// Find adjustment column index
$adjustmentIndex = array_search('Adjustment', $headers);
if ($adjustmentIndex === false) {
    echo "Kolom Adjustment tidak ditemukan!\n";
    exit(1);
}
echo "Adjustment ada di kolom index: $adjustmentIndex\n\n";

// Read some data rows and check adjustment values
$rowCount = 0;
$adjustmentCount = 0;
$nonZeroAdjustments = [];

echo "Sample data dengan adjustment:\n";
while (($row = fgetcsv($handle, 1000, ',')) !== false && $rowCount < 20) {
    $rowCount++;
    
    if (isset($row[$adjustmentIndex])) {
        $adjustmentValue = trim($row[$adjustmentIndex]);
        
        if (!empty($adjustmentValue) && $adjustmentValue != '0' && $adjustmentValue != '0.00') {
            $adjustmentCount++;
            $nonZeroAdjustments[] = $adjustmentValue;
            
            echo "Row $rowCount: ";
            echo "Container: " . ($row[1] ?? 'N/A') . " | ";
            echo "Adjustment: '$adjustmentValue' | ";
            echo "Tarif: " . ($row[5] ?? 'N/A') . "\n";
        }
    }
}

fclose($handle);

echo "\n=== HASIL ANALISIS ===\n";
echo "Total rows checked: $rowCount\n";
echo "Records dengan adjustment bukan nol: $adjustmentCount\n";
echo "Nilai adjustment yang ditemukan: " . implode(', ', array_unique($nonZeroAdjustments)) . "\n";

// Test clean function
echo "\n=== TEST CLEAN FUNCTION ===\n";
function testCleanDpeNumber($value) {
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

foreach (array_unique($nonZeroAdjustments) as $testValue) {
    $cleaned = testCleanDpeNumber($testValue);
    echo "Input: '$testValue' -> Output: $cleaned\n";
}