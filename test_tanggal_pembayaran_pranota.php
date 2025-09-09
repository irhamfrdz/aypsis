<?php
/**
 * Test format tanggal di halaman pembayaran pranota supir
 */

echo "📅 TEST FORMAT TANGGAL PEMBAYARAN PRANOTA SUPIR\n";
echo "===============================================\n\n";

echo "🧪 Testing format tanggal DateTime:\n";
echo "===================================\n";

// Test format yang akan digunakan
$testDate = '2025-09-09'; // Format dari database
$dateTime = new DateTime($testDate);
$today = new DateTime();

$oldFormat = $dateTime->format('d/m/Y');
$newFormat = $dateTime->format('d/M/Y');
$todayFormatted = $today->format('d/M/Y');
$todayISO = $today->format('Y-m-d');

echo "Tanggal dari database: $testDate\n";
echo "Format LAMA (d/m/Y): $oldFormat\n";
echo "Format BARU (d/M/Y): $newFormat\n";
echo "Tanggal hari ini: $todayISO → $todayFormatted\n\n";

echo "🔍 VERIFIKASI PERUBAHAN FILE:\n";
echo "=============================\n";

$filePath = 'resources/views/pembayaran-pranota-supir/create.blade.php';

if (file_exists($filePath)) {
    $content = file_get_contents($filePath);
    
    echo "📋 1. TANGGAL KAS:\n";
    echo "==================\n";
    // Check tanggal kas
    if (strpos($content, 'type="date"') !== false && strpos($content, 'tanggal_kas') !== false) {
        echo "⚠️  MASIH ADA: input type=\"date\" untuk tanggal_kas\n";
    } else {
        echo "✅ TIDAK ADA LAGI: input type=\"date\" untuk tanggal_kas\n";
    }
    
    if (strpos($content, "now()->format('d/M/Y')") !== false) {
        echo "✅ BERHASIL DITAMBAHKAN: now()->format('d/M/Y') untuk tanggal kas\n";
    } else {
        echo "❌ BELUM ADA: now()->format('d/M/Y') untuk tanggal kas\n";
    }
    
    if (strpos($content, 'readonly') !== false && strpos($content, 'tanggal_kas') !== false) {
        echo "✅ TANGGAL KAS READONLY: User tidak bisa mengubah\n";
    } else {
        echo "⚠️  TANGGAL KAS MASIH EDITABLE\n";
    }
    
    echo "\n📋 2. TANGGAL PRANOTA (TABEL):\n";
    echo "==============================\n";
    // Check tanggal pranota format dalam tabel
    if (strpos($content, "tanggal_pranota)->format('d/M/Y')") !== false) {
        echo "✅ BERHASIL DIUBAH: tanggal_pranota format d/M/Y\n";
    } else {
        echo "❌ BELUM DIUBAH: tanggal_pranota format\n";
    }
    
    echo "\n📋 3. JAVASCRIPT SYNC:\n";
    echo "======================\n";
    // Check JavaScript update
    if (strpos($content, 'toISOString().split') !== false) {
        echo "✅ JAVASCRIPT UPDATED: Hidden field sync diperbaiki\n";
    } else {
        echo "❌ JAVASCRIPT BELUM UPDATED\n";
    }
    
    echo "\n📊 RINGKASAN PERUBAHAN:\n";
    echo "=======================\n";
    
    // Count old formats
    $oldFormatCount = substr_count($content, "format('d/m/Y')");
    $newFormatCount = substr_count($content, "format('d/M/Y')");
    
    echo "Format lama (d/m/Y): $oldFormatCount\n";
    echo "Format baru (d/M/Y): $newFormatCount\n";
    
    if ($oldFormatCount == 0 && $newFormatCount >= 1) {
        echo "✅ SEMUA PERUBAHAN BERHASIL!\n";
    } else {
        echo "⚠️  MASIH ADA YANG PERLU DIPERBAIKI\n";
    }
    
} else {
    echo "❌ File tidak ditemukan: $filePath\n";
}

echo "\n💡 PERBANDINGAN FORMAT:\n";
echo "=======================\n";
echo "| Field          | LAMA (d/m/Y) | BARU (d/M/Y) |\n";
echo "|----------------|--------------|---------------|\n";
echo "| Tanggal Kas    | 2025-09-09   | 09/Sep/2025   |\n";
echo "| Tanggal Pranota| 09/09/2025   | 09/Sep/2025   |\n";

echo "\n🚀 HASIL YANG DIHARAPKAN:\n";
echo "=========================\n";
echo "Pada halaman pembayaran pranota supir:\n";
echo "1. ✅ Tanggal Kas: $todayFormatted (readonly, otomatis hari ini)\n";
echo "2. ✅ Tanggal Pranota (tabel): format dd/mmm/yyyy\n";
echo "3. ✅ Hidden field tetap ISO format untuk validation\n";
echo "4. ✅ Konsisten dengan halaman pranota lainnya\n";
echo "5. ✅ User-friendly dan professional\n";

echo "\n🔧 TECHNICAL DETAILS:\n";
echo "=====================\n";
echo "- Tanggal Kas: type=\"text\" + readonly\n";
echo "- Display: now()->format('d/M/Y')\n";
echo "- Hidden field: tetap Y-m-d untuk validation\n";
echo "- JavaScript: Sync otomatis dengan hari ini\n";
