<?php
/**
 * Script untuk mengkonversi file CSV comma-delimited ke semicolon-delimited
 */

echo "=== CONVERTER COMMA TO SEMICOLON ===\n";

$inputFile = 'Zona_SIAP_IMPORT.csv';
$outputFile = 'Zona_SIAP_IMPORT_SEMICOLON.csv';

if (!file_exists($inputFile)) {
    echo "ERROR: File $inputFile tidak ditemukan!\n";
    exit(1);
}

echo "Membaca file: $inputFile\n";
echo "Output file: $outputFile\n";

// Baca file CSV dengan delimiter comma
$handle = fopen($inputFile, 'r');
if (!$handle) {
    echo "ERROR: Tidak dapat membuka file $inputFile\n";
    exit(1);
}

// Siapkan file output
$outputHandle = fopen($outputFile, 'w');
if (!$outputHandle) {
    echo "ERROR: Tidak dapat membuat file $outputFile\n";
    fclose($handle);
    exit(1);
}

$totalProcessed = 0;

echo "Mengkonversi format...\n";

while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    $totalProcessed++;

    // Tulis ke file output dengan delimiter semicolon
    fputcsv($outputHandle, $row, ';');

    if ($totalProcessed <= 5) {
        echo "Row $totalProcessed: " . implode(' ; ', $row) . "\n";
    }
}

fclose($handle);
fclose($outputHandle);

echo "\n=== HASIL KONVERSI ===\n";
echo "Total baris dikonversi: $totalProcessed\n";
echo "File output: $outputFile\n";
echo "Format: Semicolon-delimited (;)\n";
echo "\nFile siap digunakan!\n";
