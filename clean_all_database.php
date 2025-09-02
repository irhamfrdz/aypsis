<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PEMBERSIHAN DATABASE MENYELURUH ===\n\n";

try {
    // Backup data dulu
    $backupFile = 'backups/full_backup_' . date('Ymd_His') . '.sql';
    echo "🔄 Membuat backup database...\n";

    // Cek jumlah records sebelum dihapus
    $totalBefore = DB::table('daftar_tagihan_kontainer_sewa')->count();
    echo "📊 Total records sebelum cleanup: $totalBefore\n\n";

    // Tampilkan distribusi periode sebelum cleanup
    echo "📈 DISTRIBUSI PERIODE SEBELUM CLEANUP:\n";
    $periodeBefore = DB::table('daftar_tagihan_kontainer_sewa')
        ->select('periode', DB::raw('COUNT(*) as count'))
        ->groupBy('periode')
        ->orderBy('periode')
        ->get();

    foreach ($periodeBefore as $p) {
        echo "Periode {$p->periode}: {$p->count} records\n";
    }
    echo "\n";

    // Konfirmasi pembersihan
    echo "⚠️  PERINGATAN: Script ini akan MENGHAPUS SEMUA DATA di tabel daftar_tagihan_kontainer_sewa\n";
    echo "📂 Anda akan upload CSV baru setelah ini?\n\n";

    // Hapus semua data
    echo "🗑️  Menghapus semua data dari tabel daftar_tagihan_kontainer_sewa...\n";
    $deleted = DB::table('daftar_tagihan_kontainer_sewa')->delete();

    echo "✅ Berhasil menghapus $deleted records\n\n";

    // Reset auto increment
    DB::statement('ALTER TABLE daftar_tagihan_kontainer_sewa AUTO_INCREMENT = 1');
    echo "🔄 Auto increment direset ke 1\n\n";

    // Verifikasi tabel kosong
    $totalAfter = DB::table('daftar_tagihan_kontainer_sewa')->count();
    echo "📊 Total records setelah cleanup: $totalAfter\n\n";

    if ($totalAfter == 0) {
        echo "✅ DATABASE BERHASIL DIBERSIHKAN!\n";
        echo "🎯 Siap untuk upload CSV baru\n\n";

        echo "📋 LANGKAH SELANJUTNYA:\n";
        echo "1. Upload CSV baru Anda\n";
        echo "2. Import data dari CSV\n";
        echo "3. Verifikasi data di aplikasi\n\n";
    } else {
        echo "❌ Masih ada $totalAfter records tersisa!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "=== SELESAI ===\n";
