<?php
/**
 * Script untuk membuat file final dengan header yang benar dan tarif yang sudah diperbaiki
 */

echo "=== ZONA FINAL CONVERTER ===\n";

$inputFile = 'Zona_SIAP_IMPORT_TARIF_BENAR.csv';
$outputFileComma = 'Zona_SIAP_IMPORT_FINAL_TARIF_BENAR_COMMA.csv';
$outputFileSemicolon = 'Zona_SIAP_IMPORT_FINAL_TARIF_BENAR_SEMICOLON.csv';

if (!file_exists($inputFile)) {
    echo "ERROR: File $inputFile tidak ditemukan!\n";
    exit(1);
}

echo "Membaca file: $inputFile\n";

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
$oldHeader = str_getcsv($lines[0], ',');
echo "Header asli: " . implode(' | ', $oldHeader) . "\n";

// Buat header baru
$newHeader = [];
foreach ($oldHeader as $field) {
    $newHeader[] = $headerMapping[$field] ?? $field;
}

echo "Header baru: " . implode(' | ', $newHeader) . "\n";

// === BUAT FILE COMMA ===
$outputHandle = fopen($outputFileComma, 'w');
if (!$outputHandle) {
    echo "ERROR: Tidak dapat membuat file $outputFileComma\n";
    exit(1);
}

// Tulis header baru
fputcsv($outputHandle, $newHeader, ',');

// Tulis data (skip header asli)
$totalProcessed = 0;
for ($i = 1; $i < count($lines); $i++) {
    $row = str_getcsv($lines[$i], ',');
    fputcsv($outputHandle, $row, ',');
    $totalProcessed++;
    
    if ($totalProcessed <= 3) {
        echo "Row $totalProcessed: " . implode(' , ', $row) . "\n";
    }
}

fclose($outputHandle);

// === BUAT FILE SEMICOLON ===
$outputHandle2 = fopen($outputFileSemicolon, 'w');
if (!$outputHandle2) {
    echo "ERROR: Tidak dapat membuat file $outputFileSemicolon\n";
    exit(1);
}

// Tulis header baru
fputcsv($outputHandle2, $newHeader, ';');

// Tulis data (skip header asli)
for ($i = 1; $i < count($lines); $i++) {
    $row = str_getcsv($lines[$i], ',');
    fputcsv($outputHandle2, $row, ';');
}

fclose($outputHandle2);

echo "\n=== HASIL KONVERSI ===\n";
echo "Total baris data: $totalProcessed\n";
echo "File comma: $outputFileComma\n";
echo "File semicolon: $outputFileSemicolon\n";
echo "Header dan tarif sekarang sudah benar!\n";
echo "- Tarif: Bulanan/Harian (bukan angka)\n";
echo "- Header: Format yang sesuai sistem import\n";