<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'aypsis');

echo "=== CARI TABEL DENGAN 'TUJUAN' ===\n\n";

$result = $mysqli->query("SHOW TABLES LIKE '%tujuan%'");

echo "Tabel yang ada:\n";
while($row = $result->fetch_row()) {
    echo "  - {$row[0]}\n";
}

echo "\n=== CEK TABEL MASTER_TUJUAN_KEGIATAN_UTAMA ===\n\n";

$result = $mysqli->query("DESC master_tujuan_kegiatan_utama");

if ($result) {
    printf("%-20s | %-20s\n", 'FIELD', 'TYPE');
    echo str_repeat("-", 45) . "\n";
    while($row = $result->fetch_assoc()) {
        printf("%-20s | %-20s\n", $row['Field'], $row['Type']);
    }
} else {
    echo "Error: " . $mysqli->error . "\n";
}

echo "\n=== DATA SAMPLE ===\n\n";

$result = $mysqli->query("SELECT * FROM master_tujuan_kegiatan_utama LIMIT 5");

if ($result) {
    printf("%-5s | %-30s | %-15s\n", 'ID', 'NAMA', 'BIAYA');
    echo str_repeat("-", 55) . "\n";
    while($row = $result->fetch_assoc()) {
        printf("%-5s | %-30s | %-15s\n",
            $row['id'] ?? '-',
            substr($row['nama_tujuan'] ?? $row['nama'] ?? '-', 0, 30),
            number_format($row['biaya'] ?? 0, 0, ',', '.')
        );
    }
}

echo "\n=== CEK HUBUNGAN DENGAN SURAT_JALANS ===\n\n";

$result = $mysqli->query("
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_NAME = 'surat_jalans'
    AND COLUMN_NAME LIKE '%tujuan%'
");

echo "Kolom tujuan di surat_jalans:\n";
while($row = $result->fetch_assoc()) {
    echo "  - {$row['COLUMN_NAME']}\n";
}
?>
