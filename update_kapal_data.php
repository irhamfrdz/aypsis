<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "🚢 UPDATING MASTER KAPAL DATA SESUAI TABEL\n";
echo "==========================================\n\n";

// Data dari tabel yang diberikan
$kapalData = [
    [
        'mother_vessel' => 'KM. SUMBER ABADI 178',
        'kode' => 'SA',
        'kapasitas_palka' => 40,
        'kapasitas_deck' => 27,
        'gross_tonnage' => 1316
    ],
    [
        'mother_vessel' => 'KM. SEKAR PERMATA',
        'kode' => 'SP',
        'kapasitas_palka' => 32,
        'kapasitas_deck' => 24,
        'gross_tonnage' => 1253
    ],
    [
        'mother_vessel' => 'KM. ALKEN PRINCESS',
        'kode' => 'AP',
        'kapasitas_palka' => 88,
        'kapasitas_deck' => 44,
        'gross_tonnage' => 1566
    ],
    [
        'mother_vessel' => 'KM. SURJAYA RAYA',
        'kode' => 'SR',
        'kapasitas_palka' => 96,
        'kapasitas_deck' => 34,
        'gross_tonnage' => 2398
    ],
    [
        'mother_vessel' => 'KM. ALEXINDO 1',
        'kode' => 'A1',
        'kapasitas_palka' => 114,
        'kapasitas_deck' => 94,
        'gross_tonnage' => 3401
    ],
    [
        'mother_vessel' => 'KM. ALEXINDO 8',
        'kode' => 'A8',
        'kapasitas_palka' => 80,
        'kapasitas_deck' => 44,
        'gross_tonnage' => 2566
    ]
];

// Update data kapal berdasarkan kode atau nama kapal
foreach ($kapalData as $data) {
    echo "🔄 Processing: {$data['mother_vessel']} ({$data['kode']})\n";

    // Cari kapal berdasarkan kode atau nama yang mirip
    $searchQuery = "SELECT id, kode, nama_kapal FROM master_kapals
                   WHERE kode = ? OR nama_kapal LIKE ? OR nama_kapal LIKE ?
                   ORDER BY
                   CASE
                       WHEN kode = ? THEN 1
                       WHEN nama_kapal LIKE ? THEN 2
                       ELSE 3
                   END
                   LIMIT 1";

    $namaPattern1 = '%' . str_replace('KM. ', '', $data['mother_vessel']) . '%';
    $namaPattern2 = '%' . $data['mother_vessel'] . '%';

    $stmt = $conn->prepare($searchQuery);
    $stmt->bind_param("sssss",
        $data['kode'],
        $namaPattern1,
        $namaPattern2,
        $data['kode'],
        $namaPattern1
    );
    $stmt->execute();
    $result = $stmt->get_result();

    if ($kapal = $result->fetch_assoc()) {
        // Update kapal yang ditemukan
        $updateQuery = "UPDATE master_kapals SET
                       kapasitas_kontainer_palka = ?,
                       kapasitas_kontainer_deck = ?,
                       gross_tonnage = ?,
                       updated_at = NOW()
                       WHERE id = ?";

        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("iidi",
            $data['kapasitas_palka'],
            $data['kapasitas_deck'],
            $data['gross_tonnage'],
            $kapal['id']
        );

        if ($updateStmt->execute()) {
            $total_teu = $data['kapasitas_palka'] + $data['kapasitas_deck'];
            echo "   ✅ Updated: {$kapal['nama_kapal']} (ID: {$kapal['id']})\n";
            echo "      📦 Palka: {$data['kapasitas_palka']} TEU\n";
            echo "      🛥️ Deck: {$data['kapasitas_deck']} TEU\n";
            echo "      💼 Total: {$total_teu} TEU\n";
            echo "      ⚖️ GT: {$data['gross_tonnage']} GT\n";
        } else {
            echo "   ❌ Error updating: " . $updateStmt->error . "\n";
        }
        $updateStmt->close();
    } else {
        // Kapal tidak ditemukan, coba insert baru
        echo "   ⚠️ Kapal tidak ditemukan, mencoba insert baru...\n";

        $insertQuery = "INSERT INTO master_kapals
                       (kode, nama_kapal, kapasitas_kontainer_palka, kapasitas_kontainer_deck, gross_tonnage, status, created_at, updated_at)
                       VALUES (?, ?, ?, ?, ?, 'aktif', NOW(), NOW())";

        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssiii",
            $data['kode'],
            $data['mother_vessel'],
            $data['kapasitas_palka'],
            $data['kapasitas_deck'],
            $data['gross_tonnage']
        );

        if ($insertStmt->execute()) {
            echo "   ✅ Inserted new: {$data['mother_vessel']}\n";
        } else {
            echo "   ❌ Error inserting: " . $insertStmt->error . "\n";
        }
        $insertStmt->close();
    }

    $stmt->close();
    echo "   " . str_repeat("-", 50) . "\n";
}

// Tampilkan hasil akhir
echo "\n📊 HASIL AKHIR - KAPAL DENGAN DATA LENGKAP:\n";
$finalQuery = "SELECT
    kode,
    nama_kapal,
    kapasitas_kontainer_palka,
    kapasitas_kontainer_deck,
    gross_tonnage,
    (COALESCE(kapasitas_kontainer_palka, 0) + COALESCE(kapasitas_kontainer_deck, 0)) as total_teu
FROM master_kapals
WHERE kapasitas_kontainer_palka IS NOT NULL
   OR kapasitas_kontainer_deck IS NOT NULL
   OR gross_tonnage IS NOT NULL
ORDER BY total_teu DESC";

$finalResult = $conn->query($finalQuery);

echo "\n| Kode | Nama Kapal                | Palka | Deck  | Total | GT   |\n";
echo "|------|---------------------------|-------|-------|-------|------|\n";

while ($row = $finalResult->fetch_assoc()) {
    printf("| %-4s | %-25s | %5s | %5s | %5s | %4s |\n",
        $row['kode'],
        substr($row['nama_kapal'], 0, 25),
        $row['kapasitas_kontainer_palka'] ?: '-',
        $row['kapasitas_kontainer_deck'] ?: '-',
        $row['total_teu'] ?: '-',
        $row['gross_tonnage'] ? number_format($row['gross_tonnage']) : '-'
    );
}

echo "\n✅ UPDATE COMPLETE!\n";

$conn->close();
?>
