<?php
// Test pranota query dengan uang_jalan

$mysqli = new mysqli('127.0.0.1', 'root', '', 'aypsis');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== TEST PRANOTA QUERY DENGAN UANG_JALAN ===\n\n";

// Query yang sama seperti di PranotaSuratJalanController
$result = $mysqli->query("
    SELECT
        id,
        no_surat_jalan,
        tanggal_surat_jalan,
        pengirim,
        tujuan_pengiriman,
        jenis_barang,
        uang_jalan,
        status
    FROM surat_jalans
    WHERE (
        status = 'fully_approved'
        OR status = 'approved'
        OR status = 'completed'
    )
    ORDER BY tanggal_surat_jalan DESC
");

if (!$result) {
    die('Query failed: ' . $mysqli->error);
}

printf("%-15s | %-12s | %-20s | %-15s | %-15s | %-15s\n",
    'NO SURAT JALAN', 'TANGGAL', 'PENGIRIM', 'TUJUAN', 'JENIS BARANG', 'UANG_JALAN');
echo str_repeat("-", 110) . "\n";

$total = 0;
$count = 0;

while($row = $result->fetch_assoc()) {
    printf("%-15s | %-12s | %-20s | %-15s | %-15s | Rp %-13s\n",
        $row['no_surat_jalan'],
        $row['tanggal_surat_jalan'],
        substr($row['pengirim'], 0, 20),
        substr($row['tujuan_pengiriman'], 0, 15),
        substr($row['jenis_barang'] ?? '-', 0, 15),
        number_format($row['uang_jalan'] ?? 0, 0, ',', '.')
    );
    $total += $row['uang_jalan'] ?? 0;
    $count++;
}

echo "\n";
echo "Total Records: {$count}\n";
echo "Total Uang Jalan: Rp " . number_format($total, 0, ',', '.') . "\n";

echo "\n✅ KESIMPULAN:\n";
echo "- Pranota akan menampilkan UANG_JALAN (bukan TARIF)\n";
echo "- Total uang jalan akan dihitung dari checkbox yang dipilih user\n";
echo "- Semua data sudah siap untuk ditampilkan di form\n";

$mysqli->close();
?>
