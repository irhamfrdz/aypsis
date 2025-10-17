<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'aypsis');

echo "=== CEK TABEL MASTER_TUJUAN_KIRIM ===\n\n";

$result = $mysqli->query("
    SELECT
        id,
        nama_tujuan,
        biaya
    FROM master_tujuan_kirims
    LIMIT 10
");

printf("%-5s | %-25s | %-15s\n", 'ID', 'NAMA TUJUAN', 'BIAYA');
echo str_repeat("-", 50) . "\n";

while($row = $result->fetch_assoc()) {
    printf("%-5s | %-25s | %-15s\n",
        $row['id'],
        substr($row['nama_tujuan'], 0, 25),
        number_format($row['biaya'], 0, ',', '.')
    );
}

echo "\n=== CEK RELATIONSHIP ===\n\n";
$result = $mysqli->query("
    SELECT
        sj.no_surat_jalan,
        sj.tujuan_pengambilan,
        mtk.nama_tujuan,
        mtk.biaya,
        sj.uang_jalan
    FROM surat_jalans sj
    LEFT JOIN master_tujuan_kirims mtk ON sj.tujuan_pengambilan = mtk.id
    LIMIT 5
");

printf("%-15s | %-15s | %-25s | %-15s | %-15s\n", 'NO SJ', 'TUJUAN_ID', 'NAMA TUJUAN', 'BIAYA MTK', 'UANG_JALAN');
echo str_repeat("-", 90) . "\n";

while($row = $result->fetch_assoc()) {
    printf("%-15s | %-15s | %-25s | %-15s | %-15s\n",
        $row['no_surat_jalan'],
        $row['tujuan_pengambilan'] ?? '-',
        substr($row['nama_tujuan'] ?? '-', 0, 25),
        number_format($row['biaya'] ?? 0, 0, ',', '.'),
        number_format($row['uang_jalan'] ?? 0, 0, ',', '.')
    );
}
?>
