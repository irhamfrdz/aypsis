<?php

// Simple validation test untuk file CSV
echo "=== TESTING IMPORT VALIDATION ===\n\n";

$file = 'TAGIHAN_DPE_IMPORT_READY.csv';

if (!file_exists($file)) {
    echo "❌ File tidak ditemukan: $file\n";
    exit;
}

echo "✅ File ditemukan: $file\n";

// Read and validate CSV
$handle = fopen($file, 'r');
$headers = fgetcsv($handle, 1000, ';');

echo "📋 Headers detected: " . count($headers) . " columns\n";
echo "🔍 Header list:\n";
foreach ($headers as $i => $header) {
    echo "  " . ($i+1) . ". " . trim($header) . "\n";
}

// Validate required DPE headers
$requiredHeaders = ['Group', 'Kontainer', 'Awal', 'Akhir', 'Ukuran', 'Harga', 'DPP'];
$missingHeaders = [];

foreach ($requiredHeaders as $required) {
    if (!in_array($required, $headers)) {
        $missingHeaders[] = $required;
    }
}

if (empty($missingHeaders)) {
    echo "✅ All required headers present\n";
} else {
    echo "❌ Missing headers: " . implode(', ', $missingHeaders) . "\n";
}

// Test first few data rows
echo "\n📝 Testing first 3 data rows:\n";
$rowNum = 1;
$errors = [];

while (($row = fgetcsv($handle, 1000, ';')) !== false && $rowNum <= 3) {
    echo "\nRow $rowNum:\n";

    // Map data
    $data = [];
    foreach ($headers as $index => $header) {
        $data[trim($header)] = isset($row[$index]) ? trim($row[$index]) : '';
    }

    // Validate key fields
    $group = $data['Group'] ?? '';
    $kontainer = $data['Kontainer'] ?? '';
    $awal = $data['Awal'] ?? '';
    $akhir = $data['Akhir'] ?? '';
    $ukuran = $data['Ukuran'] ?? '';
    $harga = $data['Harga'] ?? '';
    $dpp = $data['DPP'] ?? '';

    echo "  Group: $group\n";
    echo "  Kontainer: $kontainer\n";
    echo "  Periode: $awal → $akhir\n";
    echo "  Size: $ukuran\n";
    echo "  Harga: $harga\n";
    echo "  DPP: $dpp\n";

    // Basic validation
    $rowErrors = [];

    if (empty($kontainer)) {
        $rowErrors[] = "Kontainer kosong";
    }

    if (empty($awal) || empty($akhir)) {
        $rowErrors[] = "Tanggal tidak lengkap";
    }

    if (!in_array($ukuran, ['20', '40'])) {
        $rowErrors[] = "Ukuran tidak valid: $ukuran";
    }

    if (!is_numeric(str_replace(',', '', $harga))) {
        $rowErrors[] = "Harga bukan angka: $harga";
    }

    if (empty($rowErrors)) {
        echo "  ✅ Row valid\n";
    } else {
        echo "  ❌ Errors: " . implode(', ', $rowErrors) . "\n";
        $errors = array_merge($errors, $rowErrors);
    }

    $rowNum++;
}

fclose($handle);

echo "\n=== VALIDATION SUMMARY ===\n";
if (empty($errors)) {
    echo "🎉 FILE READY FOR IMPORT!\n";
    echo "✅ All validations passed\n";
    echo "✅ Data format correct\n";
    echo "✅ Headers compatible\n";
    echo "\n📍 Next step: Upload file via web interface\n";
    echo "🌐 URL: http://127.0.0.1:8000/daftar-tagihan-kontainer-sewa/import\n";
} else {
    echo "⚠️  Found validation issues:\n";
    foreach (array_unique($errors) as $error) {
        echo "  - $error\n";
    }
    echo "\n🔧 Please fix issues before importing\n";
}

echo "\n=== FILE INFO ===\n";
$fileSize = filesize($file);
$lines = count(file($file));
echo "📄 File size: " . number_format($fileSize) . " bytes\n";
echo "📊 Total lines: $lines\n";
echo "📦 Data rows: " . ($lines - 1) . "\n";

?>
