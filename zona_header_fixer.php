<?php
/**
 * Script untuk membuat file dengan header yang sesuai dengan mapping controller
 */

echo "=== ZONA CSV HEADER FIXER ===\n";

$inputFile = 'Zona_SIAP_IMPORT_PERIODE_SEMICOLON.csv';
$outputFile = 'Zona_SIAP_IMPORT_FINAL.csv';

if (!file_exists($inputFile)) {
    echo "ERROR: File $inputFile tidak ditemukan!\n";
    exit(1);
}

echo "Membaca file: $inputFile\n";
echo "Output file: $outputFile\n";

// Baca file CSV
$lines = file($inputFile, FILE_IGNORE_NEW_LINES);
if (!$lines) {
    echo "ERROR: Tidak dapat membaca file $inputFile\n";
    exit(1);
}

// Header mapping dari lowercase ke format yang diharapkan controller
$headerMapping = [
    'vendor' => 'Vendor',
    'nomor_kontainer' => 'Nomor Kontainer', 
    'size' => 'Size',
    'tanggal_awal' => 'Tanggal Awal',
    'tanggal_akhir' => 'Tanggal Akhir',
    'tarif' => 'Tarif',
    'adjustment' => 'Adjustment',
    'periode' => 'Periode',
    'group' => 'Group',
    'status' => 'Status'
];

// Parse header asli
$oldHeader = str_getcsv($lines[0], ';');
echo "Header asli: " . implode(' | ', $oldHeader) . "\n";

// Buat header baru
$newHeader = [];
foreach ($oldHeader as $field) {
    $newHeader[] = $headerMapping[$field] ?? $field;
}

echo "Header baru: " . implode(' | ', $newHeader) . "\n";

// Siapkan file output
$outputHandle = fopen($outputFile, 'w');
if (!$outputHandle) {
    echo "ERROR: Tidak dapat membuat file $outputFile\n";
    exit(1);
}

// Tulis header baru
fputcsv($outputHandle, $newHeader, ';');

// Tulis data (skip header asli)
$totalProcessed = 0;
for ($i = 1; $i < count($lines); $i++) {
    $row = str_getcsv($lines[$i], ';');
    fputcsv($outputHandle, $row, ';');
    $totalProcessed++;
    
    if ($totalProcessed <= 5) {
        echo "Row $totalProcessed: " . implode(' ; ', $row) . "\n";
    }
}

fclose($outputHandle);

echo "\n=== HASIL KONVERSI ===\n";
echo "Total baris data: $totalProcessed\n";
echo "File output: $outputFile\n";
echo "Header sekarang kompatibel dengan sistem import!\n";