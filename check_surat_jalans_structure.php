<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "aypsis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check surat_jalans structure
echo "=== STRUKTUR TABEL surat_jalans ===\n";
$result = $conn->query("DESC surat_jalans");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

echo "\n=== DATA DARI surat_jalans ===\n";
$result = $conn->query("SELECT * FROM surat_jalans LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        print_r($row);
        echo "---\n";
    }
}

$conn->close();
?>
