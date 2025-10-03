<?php

$file = 'C:\Users\amanda\Downloads\Tagihan Kontainer Sewa DPE.csv';
$handle = fopen($file, 'r');

echo "=== Analisis CSV Baru ===\n\n";

// Baca header
$header = fgetcsv($handle, 0, ';');
echo "Headers:\n";
foreach ($header as $idx => $col) {
    echo "  [$idx] " . trim($col) . "\n";
}

echo "\n=== Sample Data ===\n";
$count = 0;
while (($data = fgetcsv($handle, 0, ';')) !== false && $count < 10) {
    $count++;
    echo "\nBaris $count:\n";
    echo "  Group: " . $data[0] . "\n";
    echo "  Kontainer: " . $data[1] . "\n";
    echo "  Awal: " . $data[2] . "\n";
    echo "  Akhir: " . $data[3] . "\n";
    echo "  Ukuran: " . $data[4] . "\n";
    echo "  Harga: " . $data[5] . "\n";
    echo "  Periode: " . $data[6] . "\n";
    echo "  Status: " . $data[7] . "\n";
    echo "  Hari: " . $data[8] . "\n";
    echo "  DPP: " . $data[9] . "\n";
    echo "  Keterangan: " . ($data[10] ?? '') . "\n";
    echo "  QTY Disc: " . ($data[11] ?? '') . "\n";
    echo "  Adjustment: " . ($data[12] ?? '') . "\n";
    echo "  Pembulatan: " . ($data[13] ?? '') . "\n";
    echo "  PPN: " . ($data[14] ?? '') . "\n";
    echo "  PPH: " . ($data[15] ?? '') . "\n";
    echo "  Grand Total: " . ($data[16] ?? '') . "\n";
}

fclose($handle);
