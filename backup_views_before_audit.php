<?php

// Script backup file sebelum implementasi audit log
// File: backup_views_before_audit.php

echo "💾 BACKUP FILES SEBELUM IMPLEMENTASI AUDIT LOG\n";
echo "=============================================\n\n";

$backupDir = __DIR__ . '/backup_views_' . date('Y-m-d_H-i-s');

// Buat directory backup
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "📁 Directory backup dibuat: $backupDir\n\n";
}

// List file views yang akan di-backup
$viewFiles = [
    'resources/views/master-karyawan/index.blade.php',
    'resources/views/master-divisi/index.blade.php',
    'resources/views/master-kapal/index.blade.php',
    'resources/views/master-kegiatan/index.blade.php',
    'resources/views/master-pengirim/index.blade.php',
    'resources/views/users/index.blade.php',
    'resources/views/master-pricelist-sewa-kontainer/index.blade.php',
    'resources/views/pricelist-gate-in/index.blade.php',
    'resources/views/pranota/index.blade.php',
    'resources/views/pranota-supir/index.blade.php',
    'resources/views/kontainer-sewa/index.blade.php',
    'resources/views/perbaikan-kontainer/index.blade.php',
    'resources/views/pembayaran-pranota/index.blade.php',
    'resources/views/pembayaran-uang-muka/index.blade.php',
    'resources/views/tagihan-cat/index.blade.php',
    'resources/views/permohonan/index.blade.php',
    'resources/views/surat-jalan/index.blade.php',
    'resources/views/tanda-terima/index.blade.php',
    'resources/views/order/index.blade.php',
    'resources/views/permissions/index.blade.php'
];

$backedUpCount = 0;
$errorCount = 0;

foreach ($viewFiles as $file) {
    $sourcePath = __DIR__ . '/' . $file;

    if (file_exists($sourcePath)) {
        // Buat struktur directory di backup
        $backupFilePath = $backupDir . '/' . $file;
        $backupFileDir = dirname($backupFilePath);

        if (!file_exists($backupFileDir)) {
            mkdir($backupFileDir, 0755, true);
        }

        // Copy file
        if (copy($sourcePath, $backupFilePath)) {
            echo "✅ Backup: $file\n";
            $backedUpCount++;
        } else {
            echo "❌ Gagal backup: $file\n";
            $errorCount++;
        }
    } else {
        echo "⚠️  File tidak ditemukan: $file\n";
        $errorCount++;
    }
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "📊 RINGKASAN BACKUP:\n";
echo "✅ Berhasil: $backedUpCount file\n";
echo "❌ Error: $errorCount file\n";
echo "📁 Lokasi backup: $backupDir\n";

if ($backedUpCount > 0) {
    echo "\n🎉 BACKUP SELESAI!\n";
    echo "File views sudah diamankan sebelum implementasi audit log.\n";
} else {
    echo "\n⚠️  TIDAK ADA FILE YANG DI-BACKUP\n";
}

echo "\n💡 UNTUK RESTORE (jika diperlukan):\n";
echo "cp -r $backupDir/* ./\n";
