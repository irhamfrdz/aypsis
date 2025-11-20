<?php
/**
 * Script untuk membersihkan tabel pranota_tagihan_kontainer_sewa
 * dan reset status_pranota pada daftar_tagihan_kontainer_sewa
 * 
 * PERINGATAN: Script ini akan menghapus SEMUA pranota dan reset status kontainer
 * Pastikan Anda sudah melakukan backup database sebelum menjalankan script ini!
 * 
 * Cara menjalankan:
 * php clean_pranota_kontainer_sewa.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "========================================\n";
echo "SCRIPT PEMBERSIHAN PRANOTA\n";
echo "========================================\n";
echo "\n";

// Konfirmasi dari user
echo "⚠️  PERINGATAN: Script ini akan:\n";
echo "1. Menghapus SEMUA data dari tabel pranota_tagihan_kontainer_sewa\n";
echo "2. Reset status_pranota dan pranota_id pada semua kontainer\n";
echo "\n";
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
    DB::beginTransaction();

    // Cek apakah tabel pranota ada
    if (!Schema::hasTable('pranota_tagihan_kontainer_sewa')) {
        echo "❌ Error: Tabel 'pranota_tagihan_kontainer_sewa' tidak ditemukan!\n\n";
        exit(1);
    }

    // Cek apakah tabel tagihan ada
    if (!Schema::hasTable('daftar_tagihan_kontainer_sewa')) {
        echo "❌ Error: Tabel 'daftar_tagihan_kontainer_sewa' tidak ditemukan!\n\n";
        exit(1);
    }

    // Hitung jumlah pranota sebelum dihapus
    $pranotaCount = DB::table('pranota_tagihan_kontainer_sewa')->count();
    echo "📊 Jumlah pranota saat ini: {$pranotaCount} records\n";
    
    // Hitung jumlah kontainer yang akan direset
    $kontainerWithPranota = DB::table('daftar_tagihan_kontainer_sewa')
        ->whereNotNull('pranota_id')
        ->count();
    echo "📊 Jumlah kontainer yang akan direset: {$kontainerWithPranota} records\n";
    
    if ($pranotaCount == 0 && $kontainerWithPranota == 0) {
        echo "ℹ️  Tidak ada data yang perlu dibersihkan.\n\n";
        DB::rollBack();
        exit(0);
    }

    echo "\n";
    echo "Step 1: Reset status kontainer...\n";
    
    // Reset pranota_id dan status_pranota pada semua kontainer
    $updated = DB::table('daftar_tagihan_kontainer_sewa')
        ->whereNotNull('pranota_id')
        ->update([
            'pranota_id' => null,
            'status_pranota' => null,
        ]);
    
    echo "✅ {$updated} kontainer berhasil direset\n";
    
    echo "\n";
    echo "Step 2: Hapus semua pranota...\n";
    
    // Matikan foreign key check sementara
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Hapus semua pranota
    DB::table('pranota_tagihan_kontainer_sewa')->truncate();
    
    // Nyalakan kembali foreign key check
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "✅ Semua pranota berhasil dihapus\n";
    
    // Verifikasi pembersihan
    $pranotaAfter = DB::table('pranota_tagihan_kontainer_sewa')->count();
    $kontainerAfter = DB::table('daftar_tagihan_kontainer_sewa')
        ->whereNotNull('pranota_id')
        ->count();
    
    DB::commit();
    
    echo "\n";
    echo "========================================\n";
    echo "✅ PEMBERSIHAN BERHASIL!\n";
    echo "========================================\n";
    echo "Pranota sebelumnya: {$pranotaCount} records\n";
    echo "Pranota sekarang: {$pranotaAfter} records\n";
    echo "\n";
    echo "Kontainer dengan pranota sebelumnya: {$kontainerWithPranota} records\n";
    echo "Kontainer dengan pranota sekarang: {$kontainerAfter} records\n";
    echo "\n";
    echo "Semua pranota telah dihapus dan status kontainer telah direset.\n";
    echo "Auto-increment counter telah direset ke 1.\n";
    echo "\n";

} catch (\Exception $e) {
    DB::rollBack();
    
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

echo "Selesai.\n";
echo "\n";
