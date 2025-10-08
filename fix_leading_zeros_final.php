<?php
// Script untuk memperbaiki nomor seri yang kehilangan leading zeros
// dengan membandingkan panjang karakter

$inputFile = 'fix_csv_input.csv';
$outputFile = 'csv_diperbaiki.csv';

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

$header = array_shift($csvData);
$fixedData = [$header];
$fixedCount = 0;
$totalRows = count($csvData);

echo "Memproses $totalRows baris data...\n";
echo "Mencari nomor seri yang kehilangan leading zeros...\n\n";

foreach ($csvData as $index => $row) {
    $rowNumber = $index + 2;
    
    if (count($row) < 4) {
        $fixedData[] = $row;
        continue;
    }
    
    $awalan = trim($row[0]);
    $nomorSeriSaatIni = trim($row[1]);
    $akhiran = trim($row[2]);
    $nomorGabungan = trim($row[3]);
    
    // Skip jika data tidak valid
    if (empty($nomorGabungan) || strlen($nomorGabungan) != 11) {
        $fixedData[] = $row;
        continue;
    }
    
    // Extract dari nomor gabungan
    $awalanFromGabungan = substr($nomorGabungan, 0, 4);
    $nomorSeriFromGabungan = substr($nomorGabungan, 4, 6);
    $akhiranFromGabungan = substr($nomorGabungan, 10, 1);
    
    // Cek apakah nomor seri kehilangan leading zeros
    $needsFixing = false;
    
    // Cek jika nomor seri saat ini kurang dari 6 digit
    if (strlen($nomorSeriSaatIni) < 6) {
        echo "Baris $rowNumber: Nomor seri '$nomorSeriSaatIni' terlalu pendek (panjang: " . strlen($nomorSeriSaatIni) . ")\n";
        echo "  -> Seharusnya: '$nomorSeriFromGabungan' (dari gabungan: $nomorGabungan)\n";
        $row[1] = $nomorSeriFromGabungan;
        $needsFixing = true;
    }
    // Cek jika nomor seri berbeda dengan yang ada di gabungan
    else if ($nomorSeriSaatIni != $nomorSeriFromGabungan) {
        echo "Baris $rowNumber: Nomor seri tidak sesuai\n";
        echo "  -> Saat ini: '$nomorSeriSaatIni'\n";
        echo "  -> Seharusnya: '$nomorSeriFromGabungan' (dari gabungan: $nomorGabungan)\n";
        $row[1] = $nomorSeriFromGabungan;
        $needsFixing = true;
    }
    // Cek jika nomor seri dimulai dengan 0 tapi tidak memiliki leading zeros yang cukup
    else if (is_numeric($nomorSeriSaatIni) && intval($nomorSeriSaatIni) < 100000) {
        // Untuk nomor < 100000, pastikan ada leading zeros
        $nomorAsInteger = intval($nomorSeriSaatIni);
        $nomorWithLeadingZeros = str_pad($nomorAsInteger, 6, '0', STR_PAD_LEFT);
        
        if ($nomorWithLeadingZeros != $nomorSeriSaatIni) {
            echo "Baris $rowNumber: Menambahkan leading zeros\n";
            echo "  -> Dari: '$nomorSeriSaatIni' menjadi: '$nomorWithLeadingZeros'\n";
            $row[1] = $nomorWithLeadingZeros;
            $needsFixing = true;
        }
    }
    
    if ($needsFixing) {
        $fixedCount++;
    }
    
    $fixedData[] = $row;
}

// Simpan file hasil
if (($handle = fopen($outputFile, 'w')) !== FALSE) {
    foreach ($fixedData as $row) {
        fputcsv($handle, $row, ';');
    }
    fclose($handle);
    
    echo "\n=== HASIL PERBAIKAN ===\n";
    echo "Total baris diproses: $totalRows\n";
    echo "Baris yang diperbaiki: $fixedCount\n";
    echo "File hasil disimpan: $outputFile\n\n";
    
    if ($fixedCount > 0) {
        echo "✅ CSV berhasil diperbaiki!\n";
        echo "File yang sudah diperbaiki: $outputFile\n";
        echo "Silakan download dan gunakan file ini untuk import.\n";
    } else {
        echo "✅ Tidak ada masalah leading zeros ditemukan.\n";
        echo "File CSV Anda sudah dalam format yang benar.\n";
    }
    
} else {
    echo "❌ Gagal menyimpan file output: $outputFile\n";
}

// Juga buat contoh perbandingan beberapa baris
echo "\n=== CONTOH BEBERAPA BARIS PERTAMA ===\n";
$sampleRows = array_slice($fixedData, 1, 5); // Ambil 5 baris pertama (skip header)
foreach ($sampleRows as $index => $row) {
    $rowNum = $index + 2;
    if (count($row) >= 4) {
        echo "Baris $rowNum: {$row[0]} | {$row[1]} | {$row[2]} | {$row[3]}\n";
    }
}
?>