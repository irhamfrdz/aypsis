<?php

// Simple test script to check how CSV data will be processed
require_once 'vendor/autoload.php';

$csvFile = 'C:\\Users\\User\\Downloads\\m_mobils.csv';

if (!file_exists($csvFile)) {
    echo "File tidak ditemukan: {$csvFile}\n";
    exit;
}

echo "Testing CSV Import Processing...\n";
echo "================================\n\n";

$file = fopen($csvFile, 'r');
$header = fgetcsv($file, 0, ',');

echo "Header CSV:\n";
print_r($header);
echo "\n";

$rowNumber = 1;
$processedRows = 0;
$maxRows = 5; // Test first 5 rows only

while (($row = fgetcsv($file, 0, ',')) !== false && $processedRows < $maxRows) {
    $rowNumber++;
    $processedRows++;
    
    echo "Baris {$rowNumber}:\n";
    
    // Map data sesuai dengan logic controller
    $kodeAktiva = trim($row[0] ?? '');
    $nomorPolisi = trim($row[1] ?? '');
    $nik = trim($row[2] ?? '');
    $namaLengkap = trim($row[3] ?? '');
    $lokasi = trim($row[4] ?? '');
    $merek = trim($row[5] ?? '');
    $jenis = trim($row[6] ?? '');
    $tahunPembuatan = trim($row[7] ?? '');
    
    echo "  - Kode Aktiva: '{$kodeAktiva}'\n";
    echo "  - Nomor Polisi: '{$nomorPolisi}'\n";
    echo "  - NIK: '{$nik}'\n";
    echo "  - Nama Lengkap: '{$namaLengkap}'\n";
    echo "  - Lokasi: '{$lokasi}'\n";
    echo "  - Merek: '{$merek}'\n";
    echo "  - Jenis: '{$jenis}'\n";
    echo "  - Tahun: '{$tahunPembuatan}'\n";
    
    // Check conditions
    if (empty($kodeAktiva)) {
        echo "  ❌ SKIP: Kode aktiva kosong\n";
    } else {
        echo "  ✅ PROCESS: Data akan diproses dengan kode aktiva sebagai identifier\n";
        
        if (empty($nomorPolisi)) {
            echo "     - Nomor polisi kosong, akan disimpan sebagai NULL\n";
        }
        
        if (empty($nik) || empty($namaLengkap)) {
            echo "     - Data karyawan kosong, akan disimpan tanpa relasi karyawan\n";
        }
    }
    
    echo "\n";
}

fclose($file);

echo "Test selesai. Dari {$processedRows} baris yang dicek:\n";
echo "- Semua baris dengan kode aktiva akan diproses\n";
echo "- Baris tanpa nomor polisi tetap akan diimport\n";
echo "- Baris tanpa data karyawan tetap akan diimport\n";