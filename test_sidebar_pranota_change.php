<?php
/**
 * Test verifikasi perubahan sidebar "Pranota" menjadi "Pranota Supir"
 */

echo "🔍 VERIFIKASI PERUBAHAN SIDEBAR\n";
echo "===============================\n\n";

$filePath = 'resources/views/layouts/app.blade.php';

if (file_exists($filePath)) {
    $content = file_get_contents($filePath);

    echo "📋 File: $filePath\n";
    echo "====================\n\n";

    // Check for the old text
    if (strpos($content, '<span class="font-medium">Pranota</span>') !== false) {
        echo "❌ MASIH ADA: '<span class=\"font-medium\">Pranota</span>'\n";
    } else {
        echo "✅ TIDAK ADA LAGI: '<span class=\"font-medium\">Pranota</span>'\n";
    }

    // Check for the new text
    if (strpos($content, '<span class="font-medium">Pranota Supir</span>') !== false) {
        echo "✅ BERHASIL DITAMBAHKAN: '<span class=\"font-medium\">Pranota Supir</span>'\n";
    } else {
        echo "❌ BELUM ADA: '<span class=\"font-medium\">Pranota Supir</span>'\n";
    }

    echo "\n📊 SUMMARY:\n";
    echo "===========\n";

    // Count occurrences
    $oldCount = substr_count($content, '<span class="font-medium">Pranota</span>');
    $newCount = substr_count($content, '<span class="font-medium">Pranota Supir</span>');

    echo "Old text count: $oldCount\n";
    echo "New text count: $newCount\n";

    if ($oldCount == 0 && $newCount > 0) {
        echo "\n🎉 PERUBAHAN BERHASIL!\n";
        echo "✅ Sidebar sekarang menampilkan 'Pranota Supir'\n";
    } else {
        echo "\n⚠️  PERUBAHAN BELUM SEMPURNA!\n";
    }

    echo "\n💡 WHAT CHANGED:\n";
    echo "================\n";
    echo "SEBELUM: Pranota\n";
    echo "SESUDAH: Pranota Supir\n";
    echo "LOKASI:  Sidebar menu utama\n";

} else {
    echo "❌ File tidak ditemukan: $filePath\n";
}

echo "\n🚀 NEXT STEPS:\n";
echo "==============\n";
echo "1. Refresh browser untuk melihat perubahan\n";
echo "2. Periksa tampilan sidebar\n";
echo "3. Pastikan menu masih berfungsi dengan baik\n";
