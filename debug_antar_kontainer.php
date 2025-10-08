<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=aypsis', 'root', '');
    // Cek permohonan yang seharusnya muncul di Approval II dashboard
    $stmt = $pdo->prepare('
        SELECT nomor_memo, kegiatan, status, approved_by_system_1, approved_by_system_2, created_at
        FROM permohonans
        WHERE status NOT IN ("Dibatalkan")
        AND approved_by_system_1 = 1
        AND approved_by_system_2 = 0
        ORDER BY created_at DESC
        LIMIT 10
    ');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        echo 'Permohonan yang seharusnya muncul di Approval II:' . PHP_EOL;
        foreach ($results as $row) {
            echo 'Nomor: ' . $row['nomor_memo'] . PHP_EOL;
            echo 'Kegiatan: ' . $row['kegiatan'] . PHP_EOL;
            echo 'Status: ' . $row['status'] . PHP_EOL;
            echo 'System 1: ' . ($row['approved_by_system_1'] ? 'Yes' : 'No') . PHP_EOL;
            echo 'System 2: ' . ($row['approved_by_system_2'] ? 'Yes' : 'No') . PHP_EOL;
            echo '---' . PHP_EOL;
        }
    } else {
        echo 'Tidak ada permohonan yang memenuhi kriteria Approval II' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
