<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'aypsis');

echo "=== CEK NILAI UANG_JALAN ===\n\n";

$result = $mysqli->query("SELECT no_surat_jalan, uang_jalan FROM surat_jalans LIMIT 5");

printf("%-15s | %-15s | %-20s\n", 'NO SJ', 'UANG_JALAN (RAW)', 'FORMATTED');
echo str_repeat("-", 55) . "\n";

while($row = $result->fetch_assoc()) {
    $formatted = 'Rp ' . number_format($row['uang_jalan'], 0, ',', '.');
    printf("%-15s | %-15s | %-20s\n",
        $row['no_surat_jalan'],
        $row['uang_jalan'],
        $formatted
    );
}

echo "\n=== ANALISIS ===\n";
echo "Nilai di database: 675\n";
echo "Setelah number_format(675, 0, ',', '.'): 675 (tanpa pemisah karena < 1000)\n";
echo "\nUntuk menampilkan 'Rp 675.000', nilai di database harus 675000 (bukan 675)\n";
?>
