<?php
/**
 * Test format tanggal pranota dd/mmm/yyyy
 */

echo "📅 TEST FORMAT TANGGAL PRANOTA DD/MMM/YYYY\n";
echo "==========================================\n\n";

echo "🧪 Testing format tanggal Carbon:\n";
echo "=================================\n";

// Test format yang akan digunakan
$today = new DateTime();
$formatted = $today->format('d/M/Y');

echo "Tanggal hari ini: " . $today->format('Y-m-d') . "\n";
echo "Format dd/mmm/yyyy: " . $formatted . "\n\n";

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
    $testDate = new DateTime($date);
    $testFormatted = $testDate->format('d/M/Y');
    echo "✅ $date ($description) → $testFormatted\n";
}

echo "\n🔍 VERIFIKASI PERUBAHAN FILE:\n";
echo "=============================\n";

$filePath = 'resources/views/pranota-supir/create.blade.php';

if (file_exists($filePath)) {
    $content = file_get_contents($filePath);

    // Check for old format
    if (strpos($content, 'type="date"') !== false && strpos($content, 'tanggal_pranota') !== false) {
        echo "⚠️  MASIH ADA: input type=\"date\" untuk tanggal_pranota\n";
    } else {
        echo "✅ TIDAK ADA LAGI: input type=\"date\" untuk tanggal_pranota\n";
    }

    // Check for new format
    if (strpos($content, "now()->format('d/M/Y')") !== false) {
        echo "✅ BERHASIL DITAMBAHKAN: now()->format('d/M/Y')\n";
    } else {
        echo "❌ BELUM ADA: now()->format('d/M/Y')\n";
    }

    // Check for text input
    if (strpos($content, 'type="text"') !== false && strpos($content, 'tanggal_pranota') !== false) {
        echo "✅ BERHASIL DIUBAH: input type=\"text\" untuk tanggal_pranota\n";
    } else {
        echo "❌ BELUM DIUBAH: input masih bukan type=\"text\"\n";
    }

} else {
    echo "❌ File tidak ditemukan: $filePath\n";
}

echo "\n💡 KEUNTUNGAN FORMAT DD/MMM/YYYY:\n";
echo "=================================\n";
echo "✅ Konsisten dengan format di seluruh aplikasi\n";
echo "✅ Mudah dibaca dan dipahami\n";
echo "✅ Tidak ambigu (Sep = September)\n";
echo "✅ Format yang user-friendly\n";
echo "✅ Cocok untuk print dan export\n";

echo "\n🚀 HASIL YANG DIHARAPKAN:\n";
echo "=========================\n";
echo "Tanggal pranota akan menampilkan: " . $formatted . " (bukan 2025-09-09)\n";
echo "Field akan readonly dan tidak bisa diubah user\n";
echo "Format konsisten dengan tampilan lain di aplikasi\n";
