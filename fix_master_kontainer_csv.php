<?php
// Script untuk memperbaiki format CSV master kontainer
// agar sesuai dengan format yang diharapkan sistem import

$inputFile = 'master_kontainer_original.csv';
$outputFile = 'master_kontainer_fixed.csv';

if (!file_exists($inputFile)) {
    die("File input tidak ditemukan: $inputFile\n");
}

// Baca file CSV
$csvData = [];
if (($handle = fopen($inputFile, 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
        $csvData[] = $data;
    }
    fclose($handle);
}

if (empty($csvData)) {
    die("File CSV kosong atau tidak valid\n");
}

$originalHeader = array_shift($csvData); // Ambil header original
echo "Header original: " . implode(' | ', $originalHeader) . "\n\n";

// Header yang diharapkan sistem
$expectedHeader = ['Awalan Kontainer', 'Nomor Seri', 'Akhiran', 'Ukuran', 'Vendor'];

$fixedData = [$expectedHeader]; // Mulai dengan header yang benar
$processedCount = 0;
$errorCount = 0;
$skippedCount = 0;

echo "Memproses " . count($csvData) . " baris data...\n";

foreach ($csvData as $index => $row) {
    $rowNumber = $index + 2; // +2 karena mulai dari baris 2 (setelah header)
    
    // Skip empty rows
    if (empty(array_filter($row))) {
        $skippedCount++;
        continue;
    }
    
    // Pastikan baris memiliki kolom yang cukup
    if (count($row) < 5) {
        echo "Baris $rowNumber: Data tidak lengkap (" . count($row) . " kolom), dilewati\n";
        $errorCount++;
        continue;
    }
    
    try {
        // Ambil 5 kolom pertama saja (sesuai format yang diharapkan)
        $awalan = trim($row[0]);          // Awalan Kontainer
        $nomorSeri = trim($row[1]);       // Nomor Seri 
        $akhiran = trim($row[2]);         // Akhiran
        $ukuran = trim($row[3]);          // Ukuran
        $vendor = trim($row[4]);          // Vendor
        // Kolom ke-6 (Nomor Kontainer) diabaikan karena tidak diperlukan
        
        // Validasi data tidak kosong
        if (empty($awalan) || empty($nomorSeri) || empty($akhiran) || empty($ukuran) || empty($vendor)) {
            echo "Baris $rowNumber: Ada kolom yang kosong, dilewati\n";
            $errorCount++;
            continue;
        }
        
        // Validasi panjang komponen
        if (strlen($awalan) != 4) {
            echo "Baris $rowNumber: Awalan kontainer harus 4 karakter ($awalan), dilewati\n";
            $errorCount++;
            continue;
        }
        
        if (strlen($nomorSeri) != 6 || !is_numeric($nomorSeri)) {
            echo "Baris $rowNumber: Nomor seri harus 6 digit angka ($nomorSeri), dilewati\n";
            $errorCount++;
            continue;
        }
        
        if (strlen($akhiran) != 1) {
            echo "Baris $rowNumber: Akhiran kontainer harus 1 karakter ($akhiran), dilewati\n";
            $errorCount++;
            continue;
        }
        
        // Validasi ukuran
        if (!in_array($ukuran, ['20', '40'])) {
            echo "Baris $rowNumber: Ukuran harus 20 atau 40 ($ukuran), dilewati\n";
            $errorCount++;
            continue;
        }
        
        // Tambahkan data yang sudah diperbaiki
        $fixedData[] = [$awalan, $nomorSeri, $akhiran, $ukuran, $vendor];
        $processedCount++;
        
        if ($processedCount % 50 == 0) {
            echo "Diproses: $processedCount baris...\n";
        }
        
    } catch (Exception $e) {
        echo "Baris $rowNumber: Error - " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

// Simpan file hasil perbaikan
if (($handle = fopen($outputFile, 'w')) !== FALSE) {
    foreach ($fixedData as $row) {
        fputcsv($handle, $row, ';');
    }
    fclose($handle);
    
    echo "\n=== HASIL PERBAIKAN ===\n";
    echo "Total baris input: " . count($csvData) . "\n";
    echo "Baris berhasil diproses: $processedCount\n";
    echo "Baris error: $errorCount\n";
    echo "Baris kosong dilewati: $skippedCount\n";
    echo "File hasil disimpan: $outputFile\n";
    
    if ($processedCount > 0) {
        echo "\n✅ CSV berhasil diperbaiki!\n";
        echo "Silakan gunakan file: $outputFile untuk import\n";
        
        // Tampilkan contoh beberapa baris
        echo "\n=== CONTOH DATA YANG SUDAH DIPERBAIKI ===\n";
        echo "Header: " . implode(' | ', $expectedHeader) . "\n";
        
        $sampleData = array_slice($fixedData, 1, 5); // Ambil 5 baris pertama (skip header)
        foreach ($sampleData as $idx => $sample) {
            echo "Baris " . ($idx + 2) . ": " . implode(' | ', $sample) . "\n";
        }
    } else {
        echo "\n❌ Tidak ada data yang berhasil diproses.\n";
    }
    
} else {
    echo "❌ Gagal menyimpan file output: $outputFile\n";
}
?>