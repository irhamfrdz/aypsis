<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cari tabel yang berkaitan dengan approval
echo "=== TABEL YANG MENGANDUNG 'APPROVAL' ===\n";
$result = $conn->query("SHOW TABLES LIKE '%approval%'");
while ($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}

// Cari tabel yang berkaitan dengan surat jalan
echo "\n=== TABEL YANG MENGANDUNG 'SURAT_JALAN' ===\n";
$result = $conn->query("SHOW TABLES LIKE '%surat_jalan%'");
while ($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}

// Periksa struktur tabel pranota_surat_jalans
echo "\n=== STRUKTUR TABEL PRANOTA_SURAT_JALANS ===\n";
$result = $conn->query("DESCRIBE pranota_surat_jalans");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
}

$conn->close();
?>
