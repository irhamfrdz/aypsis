<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== TESTING MASTER KAPAL DENGAN DATA SAMPLE ===\n";

// Insert sample data
$sampleData = [
    [
        'kode' => 'KPL001',
        'kode_kapal' => 'MSC-001',
        'nama_kapal' => 'MSC OSCAR',
        'nickname' => 'OSCAR',
        'pelayaran' => 'Mediterranean Shipping Company',
        'kapasitas_kontainer_palka' => 8500,
        'kapasitas_kontainer_deck' => 5200,
        'gross_tonnage' => 175000.50,
        'status' => 'aktif'
    ],
    [
        'kode' => 'KPL002',
        'kode_kapal' => 'EVR-002',
        'nama_kapal' => 'EVER GIVEN',
        'nickname' => 'GIVEN',
        'pelayaran' => 'Evergreen Marine Corporation',
        'kapasitas_kontainer_palka' => 7800,
        'kapasitas_kontainer_deck' => 6200,
        'gross_tonnage' => 220000.75,
        'status' => 'aktif'
    ]
];

foreach ($sampleData as $data) {
    $stmt = $conn->prepare("
        INSERT INTO master_kapals
        (kode, kode_kapal, nama_kapal, nickname, pelayaran, kapasitas_kontainer_palka, kapasitas_kontainer_deck, gross_tonnage, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
        kapasitas_kontainer_palka = VALUES(kapasitas_kontainer_palka),
        kapasitas_kontainer_deck = VALUES(kapasitas_kontainer_deck),
        gross_tonnage = VALUES(gross_tonnage),
        updated_at = NOW()
    ");

    $stmt->bind_param("sssssiids",
        $data['kode'],
        $data['kode_kapal'],
        $data['nama_kapal'],
        $data['nickname'],
        $data['pelayaran'],
        $data['kapasitas_kontainer_palka'],
        $data['kapasitas_kontainer_deck'],
        $data['gross_tonnage'],
        $data['status']
    );

    if ($stmt->execute()) {
        echo "✅ Data {$data['nama_kapal']} berhasil diinsert/diupdate\n";
    } else {
        echo "❌ Error: " . $stmt->error . "\n";
    }
}

// Tampilkan hasil
echo "\n=== DATA KAPAL DENGAN FIELD BARU ===\n";
$result = $conn->query("
    SELECT
        kode,
        nama_kapal,
        nickname,
        pelayaran,
        kapasitas_kontainer_palka,
        kapasitas_kontainer_deck,
        gross_tonnage,
        status
    FROM master_kapals
    ORDER BY created_at DESC
    LIMIT 5
");

while ($row = $result->fetch_assoc()) {
    echo "📋 {$row['nama_kapal']} ({$row['kode']})\n";
    echo "  🏢 Pelayaran: {$row['pelayaran']}\n";
    echo "  📦 Kapasitas Palka: " . number_format($row['kapasitas_kontainer_palka']) . " TEU\n";
    echo "  🛥️ Kapasitas Deck: " . number_format($row['kapasitas_kontainer_deck']) . " TEU\n";
    echo "  ⚖️ Gross Tonnage: " . number_format($row['gross_tonnage'], 2) . " GT\n";
    echo "  📊 Status: {$row['status']}\n";
    echo "  💼 Total TEU: " . number_format($row['kapasitas_kontainer_palka'] + $row['kapasitas_kontainer_deck']) . " TEU\n";
    echo "  ------------------------\n";
}

$conn->close();
?>
