<?php
$conn = new mysqli("localhost", "root", "", "aypsis");

echo "=== CEK SURAT JALAN YANG MASIH 675 ===\n";
$result = $conn->query("
    SELECT
        id,
        no_surat_jalan,
        tujuan_pengambilan,
        tujuan_pengiriman,
        size,
        uang_jalan
    FROM surat_jalans
    WHERE uang_jalan = 675
    ORDER BY id DESC
");

if ($result->num_rows > 0) {
    echo "Ditemukan " . $result->num_rows . " surat jalan dengan uang_jalan = 675:\n";
    echo str_repeat("-", 100) . "\n";

    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, NO_SJ: {$row['no_surat_jalan']}, Pengambilan: {$row['tujuan_pengambilan']}, Size: {$row['size']}\n";
    }

    echo "\n=== UPDATE SURAT JALAN YANG MASIH 675 ===\n";

    // Update specific ones that are still 675
    $updateResult = $conn->query("
        UPDATE surat_jalans sj
        JOIN tujuan_kegiatan_utamas tku ON (LOWER(TRIM(sj.tujuan_pengambilan)) = LOWER(TRIM(tku.ke)))
        SET sj.uang_jalan = CASE
            WHEN sj.size = '20' THEN tku.uang_jalan_20ft
            WHEN sj.size = '40' THEN tku.uang_jalan_40ft
            ELSE tku.uang_jalan_20ft
        END
        WHERE sj.uang_jalan = 675
        AND sj.tujuan_pengambilan IS NOT NULL
        AND sj.tujuan_pengambilan != ''
    ");

    if ($updateResult) {
        echo "Berhasil update " . $conn->affected_rows . " record\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }

} else {
    echo "Tidak ada surat jalan dengan uang_jalan = 675\n";
}

echo "\n=== VERIFIKASI AKHIR ===\n";
$result = $conn->query("
    SELECT
        id,
        no_surat_jalan,
        tujuan_pengambilan,
        size,
        uang_jalan,
        CONCAT('Rp ', FORMAT(uang_jalan, 0, 'id_ID')) as formatted
    FROM surat_jalans
    WHERE no_surat_jalan IN ('SJ0010', 'SJ00005', 'SJ00006', 'SJ00001')
    ORDER BY id DESC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "{$row['no_surat_jalan']}: {$row['tujuan_pengambilan']} Size:{$row['size']} = {$row['formatted']}\n";
    }
}

$conn->close();
?>
