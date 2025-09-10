<?php
/**
 * Test format tanggal di halaman index pranota supir
 */

echo "📅 TEST FORMAT TANGGAL INDEX PRANOTA SUPIR\n";
echo "==========================================\n\n";

echo "🧪 Testing format tanggal DateTime:\n";
echo "===================================\n";

// Test format yang akan digunakan
$testDate = '2025-09-09'; // Format dari database
$dateTime = new DateTime($testDate);

$oldFormat = $dateTime->format('d/m/Y');
$newFormat = $dateTime->format('d/M/Y');

echo "Tanggal dari database: $testDate\n";
echo "Format LAMA (d/m/Y): $oldFormat\n";
echo "Format BARU (d/M/Y): $newFormat\n\n";

echo "📋 Contoh format berbagai tanggal:\n";
echo "==================================\n";

$testDates = [
    '2025-09-09' => 'Hari ini',
    '2025-01-01' => 'Awal tahun',
    '2025-12-31' => 'Akhir tahun',
    '2025-02-14' => 'Valentine',
    '2025-08-17' => 'Kemerdekaan'
];

foreach ($testDates as $date => $description) {
    $dateTime = new DateTime($date);
    $oldFormatted = $dateTime->format('d/m/Y');
    $newFormatted = $dateTime->format('d/M/Y');
    echo "✅ $date ($description):\n";
    echo "   LAMA: $oldFormatted\n";
    echo "   BARU: $newFormatted\n\n";
}

echo "🔍 VERIFIKASI PERUBAHAN FILE:\n";
echo "=============================\n";

$filePath = 'resources/views/pranota-supir/index.blade.php';

if (file_exists($filePath)) {
    $content = file_get_contents($filePath);

    // Check for old format
    if (strpos($content, "format('d/m/Y')") !== false) {
        echo "⚠️  MASIH ADA: format('d/m/Y')\n";
    } else {
        echo "✅ TIDAK ADA LAGI: format('d/m/Y')\n";
    }

    // Check for new format
    if (strpos($content, "format('d/M/Y')") !== false) {
        echo "✅ BERHASIL DITAMBAHKAN: format('d/M/Y')\n";
    } else {
        echo "❌ BELUM ADA: format('d/M/Y')\n";
    }

} else {
    echo "❌ File tidak ditemukan: $filePath\n";
}

echo "\n💡 PERBANDINGAN FORMAT:\n";
echo "=======================\n";
echo "| Tanggal DB  | LAMA (d/m/Y) | BARU (d/M/Y) |\n";
echo "|-------------|--------------|---------------|\n";
echo "| 2025-09-09  | 09/09/2025   | 09/Sep/2025   |\n";
echo "| 2025-01-15  | 15/01/2025   | 15/Jan/2025   |\n";
echo "| 2025-12-25  | 25/12/2025   | 25/Dec/2025   |\n";

echo "\n🚀 HASIL YANG DIHARAPKAN:\n";
echo "=========================\n";
echo "Pada tabel daftar pranota supir:\n";
echo "- Kolom 'Tanggal' akan menampilkan format: 09/Sep/2025\n";
echo "- Bukan lagi format: 09/09/2025\n";
echo "- Format konsisten dengan bagian lain aplikasi\n";
echo "- Lebih mudah dibaca dan dipahami user\n";
