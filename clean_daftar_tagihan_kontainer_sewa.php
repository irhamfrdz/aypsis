<?php
/**
 * Script untuk membersihkan tabel daftar_tagihan_kontainer_sewa
 * 
 * PERINGATAN: Script ini akan menghapus SEMUA data dari tabel daftar_tagihan_kontainer_sewa
 * Pastikan Anda sudah melakukan backup database sebelum menjalankan script ini!
 * 
 * Cara menjalankan:
 * php clean_daftar_tagihan_kontainer_sewa.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "========================================\n";
echo "SCRIPT PEMBERSIHAN DATABASE\n";
echo "Tabel: daftar_tagihan_kontainer_sewa\n";
echo "========================================\n";
echo "\n";

// Konfirmasi dari user
echo "⚠️  PERINGATAN: Script ini akan menghapus SEMUA data dari tabel daftar_tagihan_kontainer_sewa\n";
echo "Pastikan Anda sudah melakukan backup database!\n";
echo "\n";
echo "Apakah Anda yakin ingin melanjutkan? (ketik 'YES' untuk melanjutkan): ";

$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if ($confirmation !== 'YES') {
    echo "\n❌ Pembersihan dibatalkan.\n\n";
    exit(0);
}

echo "\n";
echo "Memulai pembersihan...\n";
echo "----------------------------------------\n";

try {
    // Cek apakah tabel ada
    if (!Schema::hasTable('daftar_tagihan_kontainer_sewa')) {
        echo "❌ Error: Tabel 'daftar_tagihan_kontainer_sewa' tidak ditemukan!\n\n";
        exit(1);
    }

    // Hitung jumlah data sebelum dihapus
    $countBefore = DB::table('daftar_tagihan_kontainer_sewa')->count();
    echo "📊 Jumlah data saat ini: {$countBefore} records\n";
    
    if ($countBefore == 0) {
        echo "ℹ️  Tabel sudah kosong. Tidak ada data yang perlu dihapus.\n\n";
        exit(0);
    }

    echo "\n";
    echo "Menghapus data...\n";
    
    // Matikan foreign key check sementara (untuk menghindari constraint error)
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Hapus semua data
    DB::table('daftar_tagihan_kontainer_sewa')->truncate();
    
    // Nyalakan kembali foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    // Verifikasi pembersihan
    $countAfter = DB::table('daftar_tagihan_kontainer_sewa')->count();
    
    echo "\n";
    echo "========================================\n";
    echo "✅ PEMBERSIHAN BERHASIL!\n";
    echo "========================================\n";
    echo "Data sebelumnya: {$countBefore} records\n";
    echo "Data sekarang: {$countAfter} records\n";
    echo "\n";
    echo "Tabel 'daftar_tagihan_kontainer_sewa' telah dibersihkan.\n";
    echo "Auto-increment counter telah direset ke 1.\n";
    echo "\n";

} catch (\Exception $e) {
    echo "\n";
    echo "========================================\n";
    echo "❌ ERROR SAAT PEMBERSIHAN\n";
    echo "========================================\n";
    echo "Pesan error: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    echo "\n";
    
    exit(1);
}

echo "\n";
echo "Selesai.\n";
echo "\n";
