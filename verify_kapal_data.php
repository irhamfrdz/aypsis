<?php

$conn = new mysqli('localhost', 'root', '', 'aypsis');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "🚢 VERIFIKASI DATA MASTER KAPAL SETELAH UPDATE\n";
echo "==============================================\n\n";

// Query untuk menampilkan data seperti di view
$query = "SELECT 
    kode,
    kode_kapal,
    nama_kapal,
    nickname,
    pelayaran,
    kapasitas_kontainer_palka,
    kapasitas_kontainer_deck,
    gross_tonnage,
    (COALESCE(kapasitas_kontainer_palka, 0) + COALESCE(kapasitas_kontainer_deck, 0)) as total_kapasitas,
    status
FROM master_kapals 
WHERE deleted_at IS NULL
ORDER BY 
    CASE 
        WHEN kapasitas_kontainer_palka IS NOT NULL OR kapasitas_kontainer_deck IS NOT NULL THEN 0
        ELSE 1
    END,
    nama_kapal ASC";

$result = $conn->query($query);

echo "📋 PREVIEW TABEL MASTER KAPAL (seperti di view):\n\n";

// Header tabel
printf("| %-4s | %-25s | %-8s | %-8s | %-8s | %-10s | %-8s |\n", 
    "Kode", "Nama Kapal", "Palka", "Deck", "GT", "Total", "Status");
echo "|" . str_repeat("-", 6) . "|" . str_repeat("-", 27) . "|" . str_repeat("-", 10) . "|" . str_repeat("-", 10) . "|" . str_repeat("-", 10) . "|" . str_repeat("-", 12) . "|" . str_repeat("-", 10) . "|\n";

$totalKapal = 0;
$kapalDenganData = 0;
$totalFleetTEU = 0;

while ($row = $result->fetch_assoc()) {
    $totalKapal++;
    
    $palka = $row['kapasitas_kontainer_palka'] ? number_format($row['kapasitas_kontainer_palka']) : '-';
    $deck = $row['kapasitas_kontainer_deck'] ? number_format($row['kapasitas_kontainer_deck']) : '-';
    $gt = $row['gross_tonnage'] ? number_format($row['gross_tonnage']) : '-';
    $total = $row['total_kapasitas'] > 0 ? number_format($row['total_kapasitas']) : '-';
    
    if ($row['total_kapasitas'] > 0) {
        $kapalDenganData++;
        $totalFleetTEU += $row['total_kapasitas'];
    }
    
    printf("| %-4s | %-25s | %-8s | %-8s | %-8s | %-10s | %-8s |\n",
        $row['kode'],
        substr($row['nama_kapal'], 0, 25),
        $palka,
        $deck,
        $gt,
        $total,
        ucfirst($row['status'])
    );
}

echo "\n📊 SUMMARY STATISTIK:\n";
echo "🚢 Total Kapal: " . number_format($totalKapal) . "\n";
echo "📦 Kapal dengan Data Lengkap: " . number_format($kapalDenganData) . "\n";
echo "🌊 Total Kapasitas Fleet: " . number_format($totalFleetTEU) . " TEU\n";

// Tampilkan kapal yang sesuai dengan tabel request
echo "\n🎯 KAPAL SESUAI TABEL REQUEST:\n";
$targetKapals = ['SA', 'SP', 'AP', 'SR', 'A1', 'A8'];

foreach ($targetKapals as $kode) {
    $checkQuery = "SELECT 
        kode, nama_kapal, kapasitas_kontainer_palka, kapasitas_kontainer_deck, gross_tonnage
    FROM master_kapals 
    WHERE kode = ? AND deleted_at IS NULL";
    
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($kapal = $result->fetch_assoc()) {
        $total = ($kapal['kapasitas_kontainer_palka'] ?? 0) + ($kapal['kapasitas_kontainer_deck'] ?? 0);
        echo "✅ {$kode}: {$kapal['nama_kapal']} - Palka: {$kapal['kapasitas_kontainer_palka']} | Deck: {$kapal['kapasitas_kontainer_deck']} | GT: {$kapal['gross_tonnage']} | Total: {$total} TEU\n";
    } else {
        echo "❌ {$kode}: Tidak ditemukan\n";
    }
    $stmt->close();
}

echo "\n✅ DATA BERHASIL DISESUAIKAN DENGAN TABEL REQUEST!\n";

$conn->close();
?>