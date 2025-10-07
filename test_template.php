<?php

// Test template generation untuk stock kontainer
$csvData = [
    ['Awalan Kontainer (4 karakter)', 'Nomor Seri Kontainer (6 digit)', 'Akhiran Kontainer (1 digit)', 'Nomor Seri Gabungan (11 karakter)', 'Ukuran', 'Tipe Kontainer', 'Status', 'Tahun Pembuatan', 'Keterangan'],
    ['ABCD', '123456', 'X', 'ABCD123456X', '20', 'Dry Container', 'available', '2020', 'Contoh data - hapus baris ini'],
    ['EFGH', '789012', 'Y', 'EFGH789012Y', '40', 'Reefer Container', 'rented', '2021', 'Contoh data - hapus baris ini']
];

// Generate CSV output
$output = fopen('php://temp', 'w+');

// Add BOM for UTF-8
fwrite($output, "\xEF\xBB\xBF");

foreach ($csvData as $row) {
    fputcsv($output, $row, ';');
}

rewind($output);
$csvContent = stream_get_contents($output);
fclose($output);

echo "Template CSV Output:\n";
echo "===================\n";
echo $csvContent;
echo "\nTemplate berhasil diupdate dengan kolom:\n";
echo "1. Awalan Kontainer (4 karakter)\n";
echo "2. Nomor Seri Kontainer (6 digit)\n";
echo "3. Akhiran Kontainer (1 digit)\n";
echo "4. Nomor Seri Gabungan (11 karakter)\n";
echo "5. Ukuran\n";
echo "6. Tipe Kontainer\n";
echo "7. Status\n";
echo "8. Tahun Pembuatan\n";
echo "9. Keterangan\n";