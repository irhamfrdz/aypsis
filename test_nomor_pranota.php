<?php
$conn = new mysqli("localhost", "root", "", "aypsis");

echo "=== CEK MASTER NOMOR TERAKHIR ===\n";
$result = $conn->query("SELECT * FROM nomor_terakhir WHERE modul = 'PSJ'");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Modul: {$row['modul']}, Nomor Terakhir: {$row['nomor_terakhir']}, Keterangan: {$row['keterangan']}\n";
    }
} else {
    echo "Tidak ada entry PSJ, akan dibuat otomatis saat generate nomor pertama kali.\n";
}

echo "\n=== CEK SEMUA NOMOR TERAKHIR ===\n";
$result = $conn->query("SELECT * FROM nomor_terakhir ORDER BY modul");
while ($row = $result->fetch_assoc()) {
    echo "Modul: {$row['modul']}, Nomor: {$row['nomor_terakhir']}, Keterangan: {$row['keterangan']}\n";
}

echo "\n=== TEST GENERATE NOMOR ===\n";
$bulan = date('m');
$tahun = date('y');
echo "Format yang akan digunakan: PSJ-{$bulan}{$tahun}-XXXXXX\n";
echo "Contoh: PSJ-{$bulan}{$tahun}-000001\n";

$conn->close();
?>
