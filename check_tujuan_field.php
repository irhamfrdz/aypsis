<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'aypsis');

echo "=== CEK FIELD TUJUAN DI SURAT_JALANS ===\n\n";

$result = $mysqli->query("SELECT no_surat_jalan, tujuan_pengambilan, tujuan_pengiriman FROM surat_jalans LIMIT 5");

if ($result) {
    printf("%-15s | %-20s | %-20s\n", 'NO SJ', 'TUJUAN PENGAMBILAN', 'TUJUAN PENGIRIMAN');
    echo str_repeat("-", 60) . "\n";

    while($row = $result->fetch_assoc()) {
        printf("%-15s | %-20s | %-20s\n",
            $row['no_surat_jalan'],
            $row['tujuan_pengambilan'] ?? '-',
            $row['tujuan_pengiriman'] ?? '-'
        );
    }
}

echo "\n=== KESIMPULAN ===\n";
echo "Tujuan Pengambilan: Dimana barang diambil\n";
echo "Tujuan Pengiriman: Dimana barang dikirim (DESTINATION)\n";
echo "\nPranota seharusnya menampilkan TUJUAN PENGIRIMAN\n";
?>
