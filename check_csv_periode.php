<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Check Kolom Periode di CSV ===\n\n";

$csvPath = 'C:/Users/amanda/Downloads/template_import_dpe_auto_group.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: File tidak ditemukan\n";
    exit(1);
}

$handle = fopen($csvPath, 'r');
$delimiter = ';';

$headers = [];
$rowNumber = 0;

while (($row = fgetcsv($handle, 1000, $delimiter)) !== false && $rowNumber < 15) {
    $rowNumber++;

    if ($rowNumber === 1) {
        $headers = array_map('trim', $row);
        echo "Headers: " . implode(', ', $headers) . "\n\n";
        continue;
    }

    $data = [];
    foreach ($headers as $index => $header) {
        $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
    }

    // Parse tanggal
    $start = \Carbon\Carbon::parse($data['tanggal_awal']);
    $end = \Carbon\Carbon::parse($data['tanggal_akhir']);
    $diffDays = $start->diffInDays($end) + 1;

    echo "Baris $rowNumber:\n";
    echo "  Tanggal: {$data['tanggal_awal']} s/d {$data['tanggal_akhir']}\n";
    echo "  Periode (CSV): {$data['periode']}\n";
    echo "  Selisih hari: $diffDays hari\n";
    echo "  Tarif: {$data['tarif']}\n";

    if ($data['periode'] != $diffDays) {
        echo "  ⚠️  BERBEDA! Periode CSV ≠ Jumlah hari\n";
    } else {
        echo "  ✅ SAMA! Periode CSV = Jumlah hari\n";
    }
    echo "\n";
}

fclose($handle);

echo "\n=== Kesimpulan ===\n";
echo "Periksa output di atas untuk memahami apa yang dimaksud kolom 'periode' di CSV.\n";
echo "- Jika periode = nomor urut (1,2,3...) → Gunakan jumlah hari\n";
echo "- Jika periode = jumlah hari (31,28,18...) → Gunakan nilai CSV\n";
