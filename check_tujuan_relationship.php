<?php
$conn = new mysqli("localhost", "root", "", "aypsis");

// Sample data
$result = $conn->query("
    SELECT DISTINCT
        tujuan_pengiriman,
        size
    FROM surat_jalans
    LIMIT 10
");

echo "=== TUJUAN PENGIRIMAN DATA ===\n";
while ($row = $result->fetch_assoc()) {
    echo "Tujuan: {$row['tujuan_pengiriman']}, Size: {$row['size']}\n";
}

// Check master table
echo "\n=== MASTER TABLE DATA ===\n";
$result = $conn->query("
    SELECT ke, uang_jalan_20ft, uang_jalan_40ft
    FROM tujuan_kegiatan_utamas
");

while ($row = $result->fetch_assoc()) {
    echo "Ke: {$row['ke']}, 20ft: {$row['uang_jalan_20ft']}, 40ft: {$row['uang_jalan_40ft']}\n";
}

$conn->close();
?>
