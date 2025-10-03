<?php

/**
 * Script untuk membersihkan database Daftar Tagihan Kontainer Sewa
 *
 * PERINGATAN: Script ini akan menghapus SEMUA data dari tabel daftar_tagihan_kontainer_sewa
 * Pastikan Anda sudah backup database sebelum menjalankan script ini!
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== SCRIPT PEMBERSIHAN DATABASE DAFTAR TAGIHAN KONTAINER SEWA ===\n";
echo "PERINGATAN: Script ini akan menghapus SEMUA data!\n";
echo "Pastikan Anda sudah backup database sebelum melanjutkan.\n\n";

// Tampilkan jumlah data saat ini
$currentCount = DaftarTagihanKontainerSewa::count();
echo "Jumlah data saat ini: {$currentCount} records\n\n";

if ($currentCount == 0) {
    echo "Database sudah kosong. Tidak ada yang perlu dibersihkan.\n";
    exit(0);
}

// Konfirmasi dari user
echo "Apakah Anda yakin ingin menghapus semua data? (ketik 'YES' untuk konfirmasi): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if ($confirmation !== 'YES') {
    echo "Pembersihan dibatalkan.\n";
    exit(0);
}

echo "\nMemulai pembersihan database...\n";

try {
    // Disable foreign key checks temporarily (jika ada)
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Hapus semua data dari tabel
    $deletedCount = DB::table('daftar_tagihan_kontainer_sewa')->delete();

    // Reset auto increment ID ke 1
    DB::statement('ALTER TABLE daftar_tagihan_kontainer_sewa AUTO_INCREMENT = 1;');

    // Enable foreign key checks kembali
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    echo "✅ Pembersihan berhasil!\n";
    echo "📊 Data yang dihapus: {$deletedCount} records\n";
    echo "🔄 Auto increment ID sudah direset ke 1\n";

    // Verifikasi pembersihan
    $finalCount = DaftarTagihanKontainerSewa::count();
    echo "✅ Verifikasi: Jumlah data sekarang: {$finalCount} records\n";

} catch (Exception $e) {
    echo "❌ Error saat pembersihan database: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== PEMBERSIHAN SELESAI ===\n";
echo "Database Daftar Tagihan Kontainer Sewa sudah bersih dan siap untuk import data baru.\n";
