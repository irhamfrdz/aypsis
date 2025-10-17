<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "aypsis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check tujuan_kegiatan_utamas structure
echo "=== STRUKTUR TABEL tujuan_kegiatan_utamas ===\n";
$result = $conn->query("DESC tujuan_kegiatan_utamas");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

echo "\n=== DATA DARI tujuan_kegiatan_utamas ===\n";
$result = $conn->query("SELECT * FROM tujuan_kegiatan_utamas LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $biaya = isset($row['biaya']) ? $row['biaya'] : 'N/A';
        echo "ID: {$row['id']}, Tujuan: {$row['tujuan_kegiatan']}, Biaya: {$biaya}\n";
        print_r($row);
        echo "---\n";
    }
}

// Check master_tujuan_kirim structure
echo "\n=== STRUKTUR TABEL master_tujuan_kirim ===\n";
$result = $conn->query("DESC master_tujuan_kirim");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

echo "\n=== DATA DARI master_tujuan_kirim ===\n";
$result = $conn->query("SELECT * FROM master_tujuan_kirim LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $nama = isset($row['nama']) ? $row['nama'] : 'N/A';
        $biaya = isset($row['biaya']) ? $row['biaya'] : 'N/A';
        echo "ID: {$row['id']}, Nama: {$nama}, Biaya: {$biaya}\n";
        print_r($row);
        echo "---\n";
    }
}

// Check surat_jalans to see what values they have
echo "\n=== RELASI SURAT_JALANS ===\n";
$result = $conn->query("
    SELECT sj.no_sj, sj.uang_jalan, sj.tujuan_pengambilan, sj.tujuan_pengiriman
    FROM surat_jalans sj
    LIMIT 5
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "NO_SJ: {$row['no_sj']}, Uang_Jalan: {$row['uang_jalan']}, Tujuan_Pengambilan: {$row['tujuan_pengambilan']}, Tujuan_Pengiriman: {$row['tujuan_pengiriman']}\n";
    }
}

$conn->close();
?>
