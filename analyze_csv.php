<?php

echo "=== CSV FILE ANALYSIS ===\n\n";

$csvFile = 'C:\\Users\\amanda\\Downloads\\template_import_dpe_auto_group.csv';

if (!file_exists($csvFile)) {
    echo "❌ File tidak ditemukan: {$csvFile}\n";
    exit(1);
}

echo "✅ File ditemukan: {$csvFile}\n";
echo "📊 Ukuran file: " . number_format(filesize($csvFile)) . " bytes\n\n";

// Baca header dan beberapa baris data
$handle = fopen($csvFile, 'r');
$lineNumber = 0;
$delimiter = ';'; // File Anda menggunakan semicolon

echo "📋 Analisis Struktur CSV:\n";
echo str_repeat("-", 60) . "\n";

while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE && $lineNumber < 5) {
    $lineNumber++;

    if ($lineNumber == 1) {
        echo "HEADER (Baris 1):\n";
        foreach ($row as $index => $column) {
            echo "  [{$index}] '{$column}'\n";
        }
        echo "\n";

        // Check expected columns
        $expectedColumns = ['vendor', 'nomor_kontainer', 'size', 'tanggal_awal', 'tanggal_akhir'];
        echo "📊 Validasi kolom yang dibutuhkan:\n";
        foreach ($expectedColumns as $needed) {
            $found = in_array($needed, $row);
            echo "  " . ($found ? "✅" : "❌") . " {$needed}\n";
        }
        echo "\n";
    } else {
        echo "DATA BARIS {$lineNumber}:\n";
        foreach ($row as $index => $value) {
            $columnName = ($lineNumber == 2) ? "kolom[{$index}]" : "kolom[{$index}]";
            echo "  {$columnName}: '{$value}'\n";
        }
        echo "\n";
    }
}

fclose($handle);

echo "=== KESIMPULAN ===\n\n";

// Detect actual column structure
$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle, 1000, $delimiter);
fclose($handle);

echo "File CSV Anda memiliki " . count($header) . " kolom:\n";
for ($i = 0; $i < count($header); $i++) {
    echo "  {$i}: '{$header[$i]}'\n";
}

echo "\n🔧 SARAN PERBAIKAN:\n\n";

// Mapping yang mungkin diperlukan
$mapping = [
    'vendor' => 'vendor',
    'nomor_kontainer' => 'nomor_kontainer',
    'size' => 'size',
    'tanggal_awal' => 'tanggal_awal',
    'tanggal_akhir' => 'tanggal_akhir'
];

echo "1. Pastikan kolom-kolom ini ada dalam CSV:\n";
foreach ($mapping as $needed => $csvColumn) {
    echo "   - {$needed}\n";
}

echo "\n2. Format yang disarankan:\n";
echo "   - Delimiter: ; (semicolon)\n";
echo "   - Encoding: UTF-8\n";
echo "   - Tanggal: YYYY-MM-DD atau DD-MM-YYYY\n";

echo "\n3. Template yang bisa didownload dari aplikasi akan memiliki format yang tepat.\n";

echo "\n=== SELESAI ===\n";
