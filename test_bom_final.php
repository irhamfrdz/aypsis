<?php

echo "=== Testing BOM Character Handling ===\n";

// Test CSV content dengan BOM
$csvFile = __DIR__ . '/Tagihan Kontainer Sewa DPE.csv';
echo "Testing file: " . basename($csvFile) . "\n";

if (!file_exists($csvFile)) {
    echo "File tidak ditemukan!\n";
    exit;
}

$handle = fopen($csvFile, 'r');
$delimiter = ';';

// Read headers
$headers = fgetcsv($handle, 1000, $delimiter);
echo "\nOriginal headers:\n";
foreach ($headers as $i => $header) {
    $hexValue = bin2hex($header);
    echo "  [$i] '$header' (hex: $hexValue)\n";
}

// Clean headers menggunakan method yang sama dengan controller
$cleanedHeaders = array_map(function($header) {
    // Clean BOM dari header
    $cleaned = str_replace("\xEF\xBB\xBF", "", $header);
    $cleaned = preg_replace('/^\x{FEFF}/u', '', $cleaned);
    $cleaned = preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $cleaned);
    return $cleaned;
}, $headers);

echo "\nCleaned headers:\n";
foreach ($cleanedHeaders as $i => $header) {
    $hexValue = bin2hex($header);
    echo "  [$i] '$header' (hex: $hexValue)\n";
}

// Test satu baris data
$row = fgetcsv($handle, 1000, $delimiter);
echo "\nSample row data:\n";

// Buat data array seperti di controller
$data_original = [];
$data_cleaned = [];

foreach ($headers as $index => $header) {
    $value = isset($row[$index]) ? trim($row[$index]) : '';
    $data_original[$header] = $value;

    // Use cleaned header as key
    $cleanHeader = preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $header);
    $data_cleaned[$cleanHeader] = $value;
}

echo "\nOriginal keys:\n";
foreach (array_keys($data_original) as $key) {
    $hexValue = bin2hex($key);
    echo "  '$key' (hex: $hexValue)\n";
}

echo "\nCleaned keys:\n";
foreach (array_keys($data_cleaned) as $key) {
    $hexValue = bin2hex($key);
    echo "  '$key' (hex: $hexValue)\n";
}

// Test DPE format detection dengan cleaned headers
$expectedHeaders = ['Group', 'Kontainer', 'Awal', 'Akhir', 'Ukuran'];
$hasExpectedFormat = count(array_intersect($expectedHeaders, $cleanedHeaders)) >= 3;

echo "\nDPE Format Detection:\n";
echo "Expected headers: " . implode(', ', $expectedHeaders) . "\n";
echo "Found headers: " . implode(', ', $cleanedHeaders) . "\n";
echo "Intersect count: " . count(array_intersect($expectedHeaders, $cleanedHeaders)) . "\n";
echo "Is DPE format: " . ($hasExpectedFormat ? 'YES' : 'NO') . "\n";

// Test data extraction dengan cleaned keys
echo "\nData extraction test:\n";
$container = $data_cleaned['Kontainer'] ?? 'N/A';
$group = $data_cleaned['Group'] ?? 'N/A';
$awal = $data_cleaned['Awal'] ?? 'N/A';
$akhir = $data_cleaned['Akhir'] ?? 'N/A';

echo "Container: $container\n";
echo "Group: $group\n";
echo "Period: $awal - $akhir\n";

fclose($handle);

echo "\n=== BOM Test Complete ===\n";
