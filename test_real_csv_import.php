<?php

// Test import dengan CSV format sebenarnya

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== Testing Real CSV Import ===\n\n";

// Your actual CSV format
$csvContent = "vendor;nomor_kontainer;size;group;tanggal_awal;tanggal_akhir;periode;tarif;status
DPE;CCLU3836629;20;;2025-01-21;2025-02-20;1;Bulanan;Tersedia
DPE;CCLU3836629;20;;2025-02-21;2025-03-20;2;Bulanan;Tersedia
DPE;DPEU4869769;20;;2025-03-22;2025-04-08;3;Harian;Tersedia";

$tempFile = tempnam(sys_get_temp_dir(), 'csv');
file_put_contents($tempFile, $csvContent);

echo "CSV Content:\n";
echo str_repeat("=", 80) . "\n";
echo $csvContent . "\n";
echo str_repeat("=", 80) . "\n\n";

try {
    $handle = fopen($tempFile, 'r');
    $headers = [];
    $rowNumber = 0;
    $delimiter = ';';

    echo "Processing CSV...\n\n";

    while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
        $rowNumber++;

        if ($rowNumber === 1) {
            $headers = array_map('trim', $row);
            echo "Headers: " . implode(', ', $headers) . "\n\n";
            continue;
        }

        echo "Row {$rowNumber}:\n";
        echo "  Raw data: " . implode(' | ', $row) . "\n";

        // Map data
        $data = [];
        foreach ($headers as $index => $header) {
            $data[$header] = isset($row[$index]) ? trim($row[$index]) : '';
        }

        echo "  Mapped:\n";
        foreach ($data as $key => $value) {
            echo "    {$key} = '{$value}'\n";
        }

        // Simulate what the controller does
        echo "\n  What controller expects:\n";
        echo "    vendor = '" . ($data['vendor'] ?? 'N/A') . "'\n";
        echo "    nomor_kontainer = '" . ($data['nomor_kontainer'] ?? 'N/A') . "'\n";
        echo "    size = '" . ($data['size'] ?? 'N/A') . "'\n";
        echo "    tanggal_awal = '" . ($data['tanggal_awal'] ?? 'N/A') . "'\n";
        echo "    tanggal_akhir = '" . ($data['tanggal_akhir'] ?? 'N/A') . "'\n";
        echo "    periode (from CSV) = '" . ($data['periode'] ?? 'N/A') . "' <- nomor periode\n";
        echo "    tarif (from CSV) = '" . ($data['tarif'] ?? 'N/A') . "' <- PROBLEM! Ini 'Bulanan'/'Harian', bukan harga!\n";

        // Check if it's DPE format
        $isDpeFormat = isset($data['vendor']) && $data['vendor'] === 'DPE';
        echo "\n  Is DPE format? " . ($isDpeFormat ? "YES" : "NO") . "\n";

        if ($isDpeFormat) {
            echo "  ERROR: Controller mencari kolom 'Harga' untuk tarif (harga), tapi CSV tidak punya kolom 'Harga'!\n";
            echo "  Controller akan set tarif = 0, karena getValue('Harga') returns empty!\n";
        }

        // Calculate what periode should be
        $start = Carbon::parse($data['tanggal_awal']);
        $end = Carbon::parse($data['tanggal_akhir']);
        $calculatedPeriode = $start->diffInDays($end) + 1;

        echo "\n  Calculated periode (days) = {$calculatedPeriode} hari\n";
        echo "  CSV periode column = '" . ($data['periode'] ?? '') . "' (ini nomor periode, bukan jumlah hari)\n";

        echo "\n" . str_repeat("-", 80) . "\n\n";
    }

    fclose($handle);
    unlink($tempFile);

    echo "\n=== PROBLEM IDENTIFIED ===\n\n";
    echo "CSV Anda memiliki format:\n";
    echo "  - kolom 'periode' = nomor periode (1, 2, 3...)\n";
    echo "  - kolom 'tarif' = tipe periode (Bulanan/Harian)\n";
    echo "  - TIDAK ada kolom 'Harga' = harga aktual (numerik)\n\n";

    echo "Controller mengharapkan:\n";
    echo "  - kolom 'Harga' untuk tarif/harga (nilai numerik)\n";
    echo "  - Otomatis calculate 'periode' dari tanggal (jumlah hari)\n\n";

    echo "SOLUSI:\n";
    echo "1. Tambahkan kolom 'harga' di CSV dengan nilai numerik (misal: 25000)\n";
    echo "2. ATAU ubah controller agar bisa handle format CSV Anda yang sekarang\n";
    echo "3. ATAU buat CSV template yang benar dengan kolom yang sesuai\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
