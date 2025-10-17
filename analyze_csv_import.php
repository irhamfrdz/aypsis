<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\MasterKapal;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ ANALISIS FILE CSV MASTER KAPAL                                               ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

$csvFile = 'C:\Users\amanda\Downloads\template_master_kapal (4) (1).csv';

if (!file_exists($csvFile)) {
    echo "❌ File tidak ditemukan: {$csvFile}\n";
    exit;
}

echo "📄 File: {$csvFile}\n";
echo "📊 Ukuran: " . filesize($csvFile) . " bytes\n\n";

// Parse CSV
$csvData = array_map(function($line) {
    return str_getcsv($line, ';');
}, file($csvFile));

$header = array_shift($csvData);

echo "1️⃣  HEADER CSV:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";
echo "Header aktual: " . implode(', ', $header) . "\n";
$expectedHeader = ['kode', 'kode_kapal', 'nama_kapal', 'nickname', 'pelayaran', 'catatan', 'status'];
echo "Header expected: " . implode(', ', $expectedHeader) . "\n";
if ($header === $expectedHeader) {
    echo "✅ Header COCOK\n\n";
} else {
    echo "❌ Header TIDAK COCOK\n\n";
}

echo "2️⃣  ANALISIS DATA:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";
echo "Total baris data: " . count($csvData) . "\n\n";

$issues = [];
$kodeCounts = [];

foreach ($csvData as $index => $row) {
    $rowNumber = $index + 2;
    $kode = trim($row[0]);
    $status = strtolower(trim($row[6]));

    // Check kode duplikat
    if (!isset($kodeCounts[$kode])) {
        $kodeCounts[$kode] = 0;
    }
    $kodeCounts[$kode]++;

    // Check status
    if (!in_array($status, ['aktif', 'nonaktif', 'active', 'inactive'])) {
        $issues[] = "Baris {$rowNumber}: Status '{$row[6]}' tidak valid (harus aktif/nonaktif atau active/inactive)";
    }

    // Check nama kapal kosong
    if (empty(trim($row[2]))) {
        $issues[] = "Baris {$rowNumber}: Nama kapal kosong";
    }
}

echo "3️⃣  MASALAH DITEMUKAN:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

// Kode duplikat
$duplicates = array_filter($kodeCounts, function($count) { return $count > 1; });
if (!empty($duplicates)) {
    echo "⚠️  KODE DUPLIKAT:\n";
    foreach ($duplicates as $kode => $count) {
        echo "   - Kode '{$kode}' muncul {$count} kali\n";
    }
    echo "\n";
}

// Status issues
$statusIssues = array_filter($issues, function($msg) {
    return strpos($msg, 'Status') !== false;
});

if (!empty($statusIssues)) {
    echo "⚠️  MASALAH STATUS:\n";
    $firstFive = array_slice($statusIssues, 0, 5);
    foreach ($firstFive as $issue) {
        echo "   - {$issue}\n";
    }
    if (count($statusIssues) > 5) {
        echo "   ... dan " . (count($statusIssues) - 5) . " masalah lainnya\n";
    }
    echo "\n";
}

echo "4️⃣  SOLUSI:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";
echo "1. Ganti semua 'active' menjadi 'aktif'\n";
echo "2. Perbaiki kode duplikat (baris 14-33 semua kode '13')\n";
echo "   Seharusnya: 14, 15, 16, 17, dst\n";
echo "3. Atau saya bisa update controller untuk auto-convert 'active' ke 'aktif'\n\n";

echo "5️⃣  PREVIEW 10 BARIS PERTAMA:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";
foreach (array_slice($csvData, 0, 10) as $index => $row) {
    $rowNum = $index + 2;
    echo "Baris {$rowNum}: kode={$row[0]}, nama={$row[2]}, status={$row[6]}\n";
}

echo "\n💡 Apakah Anda ingin saya perbaiki controller untuk menerima 'active' sebagai 'aktif'?\n";
