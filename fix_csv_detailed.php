<?php
// Script untuk mendeteksi dan memperbaiki nomor seri yang kehilangan leading zeros

$inputFile = 'fix_csv_input.csv';
$outputFile = 'fix_csv_output_corrected.csv';

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

$header = array_shift($csvData); // Ambil header
$fixedData = [$header]; // Mulai dengan header
$fixedCount = 0;
$errorCount = 0;
$problemRows = [];

echo "Memproses " . count($csvData) . " baris data...\n";
echo "Mencari nomor seri yang kehilangan leading zeros...\n\n";

foreach ($csvData as $index => $row) {
    $rowNumber = $index + 2; // +2 karena mulai dari baris 2 (setelah header)
    
    // Pastikan baris memiliki kolom yang cukup
    if (count($row) < 4) {
        $errorCount++;
        $fixedData[] = $row; // Tetap simpan baris yang error
        continue;
    }
    
    $awalan = trim($row[0]);
    $nomorSeriSaatIni = trim($row[1]);
    $akhiran = trim($row[2]);
    $nomorGabungan = trim($row[3]);
    
    // Skip jika nomor gabungan kosong atau tidak valid
    if (empty($nomorGabungan) || strlen($nomorGabungan) != 11) {
        $errorCount++;
        $fixedData[] = $row; // Tetap simpan baris yang error
        continue;
    }
    
    // Extract komponen dari nomor gabungan
    $awalanFromGabungan = substr($nomorGabungan, 0, 4);
    $nomorSeriFromGabungan = substr($nomorGabungan, 4, 6);
    $akhiranFromGabungan = substr($nomorGabungan, 10, 1);
    
    // Cek konsistensi dan perbaiki jika perlu
    $needsFix = false;
    $originalRow = $row;
    
    // Perbaiki awalan jika perlu
    if ($awalan != $awalanFromGabungan) {
        echo "Baris $rowNumber: Memperbaiki awalan '$awalan' menjadi '$awalanFromGabungan'\n";
        $row[0] = $awalanFromGabungan;
        $needsFix = true;
    }
    
    // Perbaiki nomor seri jika perlu (ini yang paling penting untuk leading zeros)
    if ($nomorSeriSaatIni != $nomorSeriFromGabungan) {
        echo "Baris $rowNumber: Memperbaiki nomor seri '$nomorSeriSaatIni' menjadi '$nomorSeriFromGabungan'\n";
        echo "  -> Nomor gabungan: $nomorGabungan\n";
        $row[1] = $nomorSeriFromGabungan;
        $needsFix = true;
        
        // Simpan info untuk laporan
        $problemRows[] = [
            'baris' => $rowNumber,
            'nomor_gabungan' => $nomorGabungan,
            'nomor_seri_lama' => $nomorSeriSaatIni,
            'nomor_seri_baru' => $nomorSeriFromGabungan
        ];
    }
    
    // Perbaiki akhiran jika perlu
    if ($akhiran != $akhiranFromGabungan) {
        echo "Baris $rowNumber: Memperbaiki akhiran '$akhiran' menjadi '$akhiranFromGabungan'\n";
        $row[2] = $akhiranFromGabungan;
        $needsFix = true;
    }
    
    if ($needsFix) {
        $fixedCount++;
    }
    
    $fixedData[] = $row;
}

// Juga buat versi yang dipaksa untuk memperbaiki semua nomor seri dari nomor gabungan
echo "\n=== MEMAKSA PERBAIKAN SEMUA NOMOR SERI ===\n";
$forcedData = [$header];
$forcedCount = 0;

foreach ($csvData as $index => $row) {
    $rowNumber = $index + 2;
    
    if (count($row) >= 4) {
        $nomorGabungan = trim($row[3]);
        
        if (!empty($nomorGabungan) && strlen($nomorGabungan) == 11) {
            // Extract komponen dari nomor gabungan
            $awalanFromGabungan = substr($nomorGabungan, 0, 4);
            $nomorSeriFromGabungan = substr($nomorGabungan, 4, 6);
            $akhiranFromGabungan = substr($nomorGabungan, 10, 1);
            
            // Paksa update semua komponen
            $row[0] = $awalanFromGabungan;   // awalan
            $row[1] = $nomorSeriFromGabungan; // nomor seri (dengan leading zeros)
            $row[2] = $akhiranFromGabungan;   // akhiran
            
            $forcedCount++;
        }
    }
    
    $forcedData[] = $row;
}

// Simpan file hasil perbaikan normal
if (($handle = fopen($outputFile, 'w')) !== FALSE) {
    foreach ($fixedData as $row) {
        fputcsv($handle, $row, ';');
    }
    fclose($handle);
}

// Simpan file hasil perbaikan paksa
$forcedOutputFile = 'fix_csv_forced_output.csv';
if (($handle = fopen($forcedOutputFile, 'w')) !== FALSE) {
    foreach ($forcedData as $row) {
        fputcsv($handle, $row, ';');
    }
    fclose($handle);
}

echo "\n=== HASIL PERBAIKAN ===\n";
echo "Total baris diproses: " . count($csvData) . "\n";
echo "Baris diperbaiki (deteksi otomatis): $fixedCount\n";
echo "Baris diperbaiki (paksa semua): $forcedCount\n";
echo "Baris error: $errorCount\n";
echo "\nFile hasil:\n";
echo "- Perbaikan otomatis: $outputFile\n";
echo "- Perbaikan paksa: $forcedOutputFile\n";

if (!empty($problemRows)) {
    echo "\n=== DETAIL NOMOR SERI YANG DIPERBAIKI ===\n";
    foreach ($problemRows as $problem) {
        echo "Baris {$problem['baris']}: {$problem['nomor_seri_lama']} → {$problem['nomor_seri_baru']} (dari {$problem['nomor_gabungan']})\n";
    }
}

echo "\n✅ Silakan gunakan file: $forcedOutputFile\n";
echo "File ini memastikan semua nomor seri sesuai dengan nomor gabungan dengan leading zeros yang benar.\n";
?>