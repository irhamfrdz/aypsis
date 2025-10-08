<?php
// Script untuk memperbaiki nomor seri kontainer dengan leading zeros
// berdasarkan nomor seri gabungan

$inputFile = 'fix_csv_input.csv';
$outputFile = 'fix_csv_output.csv';

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

echo "Memproses " . count($csvData) . " baris data...\n";

foreach ($csvData as $index => $row) {
    $rowNumber = $index + 2; // +2 karena mulai dari baris 2 (setelah header)

    // Pastikan baris memiliki kolom yang cukup
    if (count($row) < 4) {
        echo "Baris $rowNumber: Data tidak lengkap, dilewati\n";
        $errorCount++;
        continue;
    }

    $awalan = trim($row[0]);
    $nomorSeriSaatIni = trim($row[1]);
    $akhiran = trim($row[2]);
    $nomorGabungan = trim($row[3]);

    // Skip jika nomor gabungan kosong
    if (empty($nomorGabungan)) {
        echo "Baris $rowNumber: Nomor gabungan kosong, dilewati\n";
        $errorCount++;
        continue;
    }

    // Validasi format nomor gabungan (harus 11 karakter)
    if (strlen($nomorGabungan) != 11) {
        echo "Baris $rowNumber: Nomor gabungan tidak valid (panjang: " . strlen($nomorGabungan) . "), dilewati\n";
        $errorCount++;
        continue;
    }

    // Extract komponen dari nomor gabungan
    $awalanFromGabungan = substr($nomorGabungan, 0, 4);
    $nomorSeriFromGabungan = substr($nomorGabungan, 4, 6);
    $akhiranFromGabungan = substr($nomorGabungan, 10, 1);

    // Validasi konsistensi awalan
    if ($awalan != $awalanFromGabungan) {
        echo "Baris $rowNumber: Awalan tidak konsisten ($awalan vs $awalanFromGabungan)\n";
        $errorCount++;
        continue;
    }

    // Validasi konsistensi akhiran
    if ($akhiran != $akhiranFromGabungan) {
        echo "Baris $rowNumber: Akhiran tidak konsisten ($akhiran vs $akhiranFromGabungan)\n";
        $errorCount++;
        continue;
    }

    // Cek apakah nomor seri perlu diperbaiki
    if ($nomorSeriSaatIni != $nomorSeriFromGabungan) {
        echo "Baris $rowNumber: Memperbaiki nomor seri '$nomorSeriSaatIni' menjadi '$nomorSeriFromGabungan'\n";
        $row[1] = $nomorSeriFromGabungan; // Update nomor seri
        $fixedCount++;
    }

    $fixedData[] = $row;
}

// Simpan file hasil perbaikan
if (($handle = fopen($outputFile, 'w')) !== FALSE) {
    foreach ($fixedData as $row) {
        fputcsv($handle, $row, ';');
    }
    fclose($handle);

    echo "\n=== HASIL PERBAIKAN ===\n";
    echo "Total baris diproses: " . count($csvData) . "\n";
    echo "Baris diperbaiki: $fixedCount\n";
    echo "Baris error: $errorCount\n";
    echo "File hasil disimpan: $outputFile\n";

    if ($fixedCount > 0) {
        echo "\n✅ CSV berhasil diperbaiki!\n";
        echo "Silakan gunakan file: $outputFile\n";
    } else {
        echo "\n⚠️ Tidak ada nomor seri yang perlu diperbaiki.\n";
    }

} else {
    echo "❌ Gagal menyimpan file output: $outputFile\n";
}
?>
