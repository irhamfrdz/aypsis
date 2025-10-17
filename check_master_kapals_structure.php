<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== STRUKTUR TABEL MASTER_KAPALS ===\n";
$result = $conn->query("DESCRIBE master_kapals");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
}

$conn->close();
?>
