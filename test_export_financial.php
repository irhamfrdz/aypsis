<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;

echo "Testing CSV export with financial data...\n\n";

// Create temporary CSV file
$exportFile = 'test_export_result.csv';
$file = fopen($exportFile, 'w');

// Add BOM for UTF-8
fputs($file, "\xEF\xBB\xBF");

// Write header
fputcsv($file, [
    'Group',
    'Vendor',
    'Nomor Kontainer',
    'Size',
    'Tanggal Awal',
    'Tanggal Akhir',
    'Periode',
    'Masa',
    'Tarif',
    'Status',
    'DPP',
    'Adjustment',
    'DPP Nilai Lain',
    'PPN',
    'PPH',
    'Grand Total',
    'Status Pranota',
    'Pranota ID'
], ';');

// Get data from database
$tagihans = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'CBHU3952697')
    ->orderBy('periode')
    ->get();

echo "Found " . $tagihans->count() . " records to export\n\n";

// Write data
foreach ($tagihans as $tagihan) {
    $row = [
        $tagihan->group ?? '',
        $tagihan->vendor ?? '',
        $tagihan->nomor_kontainer ?? '',
        $tagihan->size ?? '',
        $tagihan->tanggal_awal ? \Carbon\Carbon::parse($tagihan->tanggal_awal)->format('d-m-Y') : '',
        $tagihan->tanggal_akhir ? \Carbon\Carbon::parse($tagihan->tanggal_akhir)->format('d-m-Y') : '',
        $tagihan->periode ?? '',
        $tagihan->masa ?? '',
        $tagihan->tarif ?? '',
        $tagihan->status ?? '',
        $tagihan->dpp ?? 0,
        $tagihan->adjustment ?? 0,
        $tagihan->dpp_nilai_lain ?? 0,
        $tagihan->ppn ?? 0,
        $tagihan->pph ?? 0,
        $tagihan->grand_total ?? 0,
        $tagihan->status_pranota ?? '',
        $tagihan->pranota_id ?? ''
    ];

    fputcsv($file, $row, ';');

    echo "Exported Periode {$tagihan->periode}:\n";
    echo "  DPP: {$row[10]}\n";
    echo "  PPN: {$row[13]}\n";
    echo "  PPH: {$row[14]}\n";
    echo "  Grand Total: {$row[15]}\n\n";
}

fclose($file);

echo "\nExport saved to: {$exportFile}\n";
echo "File size: " . filesize($exportFile) . " bytes\n";

// Read back the file to verify
echo "\n\nVerifying exported CSV content:\n";
$handle = fopen($exportFile, 'r');
$lineNum = 0;
while (($data = fgetcsv($handle, 1000, ';')) !== false) {
    $lineNum++;
    if ($lineNum == 1) {
        echo "Headers: " . implode(', ', $data) . "\n\n";
    } else {
        echo "Row {$lineNum}: Container={$data[2]}, Periode={$data[6]}, DPP={$data[10]}, PPN={$data[13]}, PPH={$data[14]}, Grand Total={$data[15]}\n";
    }
}
fclose($handle);
