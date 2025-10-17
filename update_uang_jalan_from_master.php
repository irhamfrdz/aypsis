<?php
// Update uang_jalan values from master table
$conn = new mysqli("localhost", "root", "", "aypsis");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== UPDATE UANG_JALAN FROM MASTER TABLE ===\n\n";

// Get all surat jalans with incorrect/old values
$result = $conn->query("
    SELECT
        sj.id,
        sj.no_surat_jalan,
        sj.tujuan_pengambilan,
        sj.tujuan_pengiriman,
        sj.size,
        sj.uang_jalan as uang_jalan_lama,
        tku.uang_jalan_20ft,
        tku.uang_jalan_40ft
    FROM surat_jalans sj
    LEFT JOIN tujuan_kegiatan_utamas tku
        ON (LOWER(TRIM(sj.tujuan_pengambilan)) = LOWER(TRIM(tku.ke)))
    WHERE sj.uang_jalan IS NULL OR sj.uang_jalan < 1000
");

if ($result) {
    $updateCount = 0;
    echo "Surat Jalan yang perlu di-update:\n";
    echo str_repeat("-", 120) . "\n";

    while ($row = $result->fetch_assoc()) {
        $noSj = $row['no_surat_jalan'];
        $tujuanPengambilan = $row['tujuan_pengambilan'];
        $tujuanPengiriman = $row['tujuan_pengiriman'];
        $size = $row['size'];
        $uangJalanLama = $row['uang_jalan_lama'] ?? 'NULL';

        // Determine new uang_jalan
        $uangJalanBaru = 0;
        if ($size == '20' && $row['uang_jalan_20ft']) {
            $uangJalanBaru = $row['uang_jalan_20ft'];
        } elseif (($size == '40' || $size == '45') && $row['uang_jalan_40ft']) {
            $uangJalanBaru = $row['uang_jalan_40ft'];
        } elseif ($row['uang_jalan_20ft']) {
            // Default to 20ft if no match
            $uangJalanBaru = $row['uang_jalan_20ft'];
        }

        if ($uangJalanBaru > 0) {
            printf("ID: %3d | NO_SJ: %-10s | Pengambilan: %-15s | Pengiriman: %-15s | Size: %2s | Lama: %8s | Baru: %10.2f\n",
                $row['id'], $noSj, substr($tujuanPengambilan, 0, 15), substr($tujuanPengiriman, 0, 15), $size, $uangJalanLama, $uangJalanBaru);

            // Update the surat_jalans table
            $updateStmt = $conn->prepare("UPDATE surat_jalans SET uang_jalan = ? WHERE id = ?");
            $updateStmt->bind_param("di", $uangJalanBaru, $row['id']);
            if ($updateStmt->execute()) {
                $updateCount++;
            } else {
                echo "ERROR updating ID {$row['id']}: " . $updateStmt->error . "\n";
            }
            $updateStmt->close();
        } else {
            printf("ID: %3d | NO_SJ: %-10s | Pengambilan: %-15s | Pengiriman: %-15s | Size: %2s | Status: TIDAK DITEMUKAN\n",
                $row['id'], $noSj, substr($tujuanPengambilan, 0, 15), substr($tujuanPengiriman, 0, 15), $size);
        }
    }

    echo str_repeat("-", 120) . "\n";
    echo "\nTotal updated: $updateCount records\n";
}

echo "\n=== VERIFIKASI HASIL UPDATE ===\n";
$verifyResult = $conn->query("
    SELECT
        id,
        no_surat_jalan,
        tujuan_pengambilan,
        tujuan_pengiriman,
        size,
        uang_jalan,
        CONCAT('Rp ', FORMAT(uang_jalan, 0, 'id_ID')) as uang_jalan_format
    FROM surat_jalans
    ORDER BY id DESC
    LIMIT 10
");

if ($verifyResult) {
    echo "\nSurat Jalan setelah update (10 terbaru):\n";
    echo str_repeat("-", 120) . "\n";
    printf("%-5s | %-12s | %-15s | %-15s | %-4s | %-12s | %-18s\n", "ID", "NO_SJ", "PENGAMBILAN", "PENGIRIMAN", "SIZE", "UANG_JALAN", "FORMAT");
    echo str_repeat("-", 120) . "\n";

    while ($row = $verifyResult->fetch_assoc()) {
        printf("%-5d | %-12s | %-15s | %-15s | %-4s | %12.2f | %-18s\n",
            $row['id'],
            $row['no_surat_jalan'],
            substr($row['tujuan_pengambilan'], 0, 15),
            substr($row['tujuan_pengiriman'], 0, 15),
            $row['size'],
            $row['uang_jalan'],
            $row['uang_jalan_format']
        );
    }
}

$conn->close();
?>
