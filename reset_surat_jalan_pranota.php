<?php
/**
 * Script untuk mereset status surat jalan yang sudah di-pranota
 * agar muncul kembali di form create pranota uang rit
 * 
 * Usage:
 * 1. Jika ingin reset berdasarkan nomor pranota yang dihapus:
 *    php artisan tinker
 *    include 'reset_surat_jalan_pranota.php';
 *    resetByNomorPranota('PUR-01-26-000001');
 * 
 * 2. Jika ingin reset berdasarkan nomor surat jalan:
 *    php artisan tinker
 *    include 'reset_surat_jalan_pranota.php';
 *    resetBySuratJalan(['SJ-001', 'SJ-002', 'SJ-003']);
 * 
 * 3. Atau jalankan langsung dengan PHP CLI (sesuaikan DB config):
 *    php reset_surat_jalan_pranota.php
 */

// Jika dijalankan langsung (bukan dari tinker)
if (php_sapi_name() === 'cli' && !class_exists('DB')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
}

use Illuminate\Support\Facades\DB;
use App\Models\SuratJalan;

/**
 * Reset surat jalan berdasarkan nomor pranota yang dihapus
 * 
 * @param string $nomorPranota
 * @return array
 */
function resetByNomorPranota($nomorPranota)
{
    try {
        DB::beginTransaction();
        
        // Cari semua surat jalan yang terkait dengan nomor pranota ini
        $pranotaRecords = DB::table('pranota_uang_rits')
            ->where('nomor_pranota', $nomorPranota)
            ->whereNotNull('surat_jalan_id')
            ->get();
        
        if ($pranotaRecords->isEmpty()) {
            echo "❌ Tidak ada surat jalan yang terkait dengan nomor pranota: {$nomorPranota}\n";
            DB::rollBack();
            return ['success' => false, 'message' => 'Pranota tidak ditemukan'];
        }
        
        $suratJalanIds = $pranotaRecords->pluck('surat_jalan_id')->toArray();
        $count = count($suratJalanIds);
        
        echo "🔍 Ditemukan {$count} surat jalan terkait pranota: {$nomorPranota}\n";
        
        // Reset status pembayaran uang rit ke 'belum_dibayar'
        $updated = DB::table('surat_jalans')
            ->whereIn('id', $suratJalanIds)
            ->update([
                'status_pembayaran_uang_rit' => 'belum_dibayar',
                'updated_at' => now()
            ]);
        
        echo "✅ Berhasil reset {$updated} surat jalan ke status 'belum_dibayar'\n";
        
        // Hapus record dari pranota_uang_rits
        $deleted = DB::table('pranota_uang_rits')
            ->where('nomor_pranota', $nomorPranota)
            ->whereNotNull('surat_jalan_id')
            ->delete();
        
        echo "✅ Berhasil hapus {$deleted} record dari pranota_uang_rits\n";
        
        // Tampilkan detail surat jalan yang direset
        $suratJalans = DB::table('surat_jalans')
            ->whereIn('id', $suratJalanIds)
            ->select('id', 'no_surat_jalan', 'supir', 'status_pembayaran_uang_rit')
            ->get();
        
        echo "\n📋 Detail surat jalan yang direset:\n";
        foreach ($suratJalans as $sj) {
            echo "   - ID: {$sj->id} | No: {$sj->no_surat_jalan} | Supir: {$sj->supir} | Status: {$sj->status_pembayaran_uang_rit}\n";
        }
        
        DB::commit();
        
        echo "\n✨ Sukses! Surat jalan sekarang akan muncul kembali di form create pranota.\n";
        
        return [
            'success' => true,
            'message' => "Berhasil reset {$count} surat jalan",
            'surat_jalan_ids' => $suratJalanIds
        ];
        
    } catch (\Exception $e) {
        DB::rollBack();
        echo "❌ Error: " . $e->getMessage() . "\n";
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Reset surat jalan berdasarkan array nomor surat jalan
 * 
 * @param array $nomorSuratJalans
 * @return array
 */
function resetBySuratJalan($nomorSuratJalans)
{
    try {
        DB::beginTransaction();
        
        if (!is_array($nomorSuratJalans)) {
            $nomorSuratJalans = [$nomorSuratJalans];
        }
        
        // Cari surat jalan berdasarkan nomor
        $suratJalans = DB::table('surat_jalans')
            ->whereIn('no_surat_jalan', $nomorSuratJalans)
            ->get();
        
        if ($suratJalans->isEmpty()) {
            echo "❌ Tidak ada surat jalan yang ditemukan dengan nomor tersebut\n";
            DB::rollBack();
            return ['success' => false, 'message' => 'Surat jalan tidak ditemukan'];
        }
        
        $suratJalanIds = $suratJalans->pluck('id')->toArray();
        $count = count($suratJalanIds);
        
        echo "🔍 Ditemukan {$count} surat jalan\n";
        
        // Reset status pembayaran uang rit ke 'belum_dibayar'
        $updated = DB::table('surat_jalans')
            ->whereIn('id', $suratJalanIds)
            ->update([
                'status_pembayaran_uang_rit' => 'belum_dibayar',
                'updated_at' => now()
            ]);
        
        echo "✅ Berhasil reset {$updated} surat jalan ke status 'belum_dibayar'\n";
        
        // Hapus record dari pranota_uang_rits
        $deleted = DB::table('pranota_uang_rits')
            ->whereIn('surat_jalan_id', $suratJalanIds)
            ->delete();
        
        if ($deleted > 0) {
            echo "✅ Berhasil hapus {$deleted} record dari pranota_uang_rits\n";
        } else {
            echo "ℹ️  Tidak ada record pranota yang dihapus (mungkin sudah dihapus sebelumnya)\n";
        }
        
        // Tampilkan detail surat jalan yang direset
        echo "\n📋 Detail surat jalan yang direset:\n";
        foreach ($suratJalans as $sj) {
            echo "   - ID: {$sj->id} | No: {$sj->no_surat_jalan} | Supir: {$sj->supir} | Status: belum_dibayar\n";
        }
        
        DB::commit();
        
        echo "\n✨ Sukses! Surat jalan sekarang akan muncul kembali di form create pranota.\n";
        
        return [
            'success' => true,
            'message' => "Berhasil reset {$count} surat jalan",
            'surat_jalan_ids' => $suratJalanIds
        ];
        
    } catch (\Exception $e) {
        DB::rollBack();
        echo "❌ Error: " . $e->getMessage() . "\n";
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Reset surat jalan bongkaran berdasarkan array nomor surat jalan
 * 
 * @param array $nomorSuratJalans
 * @return array
 */
function resetBySuratJalanBongkaran($nomorSuratJalans)
{
    try {
        DB::beginTransaction();
        
        if (!is_array($nomorSuratJalans)) {
            $nomorSuratJalans = [$nomorSuratJalans];
        }
        
        // Cari surat jalan bongkaran berdasarkan nomor
        $suratJalans = DB::table('surat_jalan_bongkarans')
            ->whereIn('nomor_surat_jalan', $nomorSuratJalans)
            ->get();
        
        if ($suratJalans->isEmpty()) {
            echo "❌ Tidak ada surat jalan bongkaran yang ditemukan dengan nomor tersebut\n";
            DB::rollBack();
            return ['success' => false, 'message' => 'Surat jalan bongkaran tidak ditemukan'];
        }
        
        $suratJalanIds = $suratJalans->pluck('id')->toArray();
        $count = count($suratJalanIds);
        
        echo "🔍 Ditemukan {$count} surat jalan bongkaran\n";
        
        // Reset status pembayaran uang rit ke 'belum_dibayar'
        $updated = DB::table('surat_jalan_bongkarans')
            ->whereIn('id', $suratJalanIds)
            ->update([
                'status_pembayaran_uang_rit' => 'belum_dibayar',
                'updated_at' => now()
            ]);
        
        echo "✅ Berhasil reset {$updated} surat jalan bongkaran ke status 'belum_dibayar'\n";
        
        // Hapus record dari pranota_uang_rits
        $deleted = DB::table('pranota_uang_rits')
            ->whereIn('surat_jalan_bongkaran_id', $suratJalanIds)
            ->delete();
        
        if ($deleted > 0) {
            echo "✅ Berhasil hapus {$deleted} record dari pranota_uang_rits\n";
        } else {
            echo "ℹ️  Tidak ada record pranota yang dihapus (mungkin sudah dihapus sebelumnya)\n";
        }
        
        // Tampilkan detail surat jalan yang direset
        echo "\n📋 Detail surat jalan bongkaran yang direset:\n";
        foreach ($suratJalans as $sj) {
            echo "   - ID: {$sj->id} | No: {$sj->nomor_surat_jalan} | Supir: {$sj->supir} | Status: belum_dibayar\n";
        }
        
        DB::commit();
        
        echo "\n✨ Sukses! Surat jalan bongkaran sekarang akan muncul kembali di form create pranota.\n";
        
        return [
            'success' => true,
            'message' => "Berhasil reset {$count} surat jalan bongkaran",
            'surat_jalan_ids' => $suratJalanIds
        ];
        
    } catch (\Exception $e) {
        DB::rollBack();
        echo "❌ Error: " . $e->getMessage() . "\n";
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Reset semua surat jalan dalam rentang tanggal tertentu
 * 
 * @param string $startDate Format: Y-m-d
 * @param string $endDate Format: Y-m-d
 * @return array
 */
function resetByDateRange($startDate, $endDate)
{
    try {
        DB::beginTransaction();
        
        // Cari pranota dalam rentang tanggal
        $pranotaRecords = DB::table('pranota_uang_rits')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('surat_jalan_id')
            ->get();
        
        if ($pranotaRecords->isEmpty()) {
            echo "❌ Tidak ada pranota dalam rentang tanggal {$startDate} - {$endDate}\n";
            DB::rollBack();
            return ['success' => false, 'message' => 'Tidak ada data'];
        }
        
        $suratJalanIds = $pranotaRecords->pluck('surat_jalan_id')->unique()->toArray();
        $count = count($suratJalanIds);
        
        echo "🔍 Ditemukan {$count} surat jalan unik dalam rentang tanggal {$startDate} - {$endDate}\n";
        
        // Reset status pembayaran uang rit ke 'belum_dibayar'
        $updated = DB::table('surat_jalans')
            ->whereIn('id', $suratJalanIds)
            ->update([
                'status_pembayaran_uang_rit' => 'belum_dibayar',
                'updated_at' => now()
            ]);
        
        echo "✅ Berhasil reset {$updated} surat jalan ke status 'belum_dibayar'\n";
        
        // Hapus record dari pranota_uang_rits
        $deleted = DB::table('pranota_uang_rits')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('surat_jalan_id')
            ->delete();
        
        echo "✅ Berhasil hapus {$deleted} record dari pranota_uang_rits\n";
        
        DB::commit();
        
        echo "\n✨ Sukses! Surat jalan sekarang akan muncul kembali di form create pranota.\n";
        
        return [
            'success' => true,
            'message' => "Berhasil reset {$count} surat jalan",
            'surat_jalan_ids' => $suratJalanIds
        ];
        
    } catch (\Exception $e) {
        DB::rollBack();
        echo "❌ Error: " . $e->getMessage() . "\n";
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// ==================== SCRIPT LANGSUNG RESET ====================

// Jika file ini dijalankan langsung, langsung reset berdasarkan tanggal
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    echo "\n";
    echo "╔══════════════════════════════════════════════════════════════╗\n";
    echo "║       SCRIPT RESET STATUS SURAT JALAN PRANOTA UANG RIT      ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
    $handle = fopen("php://stdin", "r");
    
    echo "Masukkan tanggal mulai (YYYY-MM-DD): ";
    $startDate = trim(fgets($handle));
    
    echo "Masukkan tanggal akhir (YYYY-MM-DD): ";
    $endDate = trim(fgets($handle));
    
    if (!empty($startDate) && !empty($endDate)) {
        resetByDateRange($startDate, $endDate);
    } else {
        echo "❌ Tanggal tidak boleh kosong!\n";
    }
    
    fclose($handle);
    echo "\n";
}

// ==================== ATAU GUNAKAN DARI TINKER ====================
/*
Cara menggunakan dari Laravel Tinker:

1. Buka terminal dan jalankan:
   php artisan tinker

2. Load script ini:
   include 'reset_surat_jalan_pranota.php';

3. Pilih salah satu fungsi:

   // Reset berdasarkan nomor pranota
   resetByNomorPranota('PUR-01-26-000001');

   // Reset berdasarkan nomor surat jalan (satu atau lebih)
   resetBySuratJalan(['SJ-001', 'SJ-002']);
   // atau
   resetBySuratJalan('SJ-001');

   // Reset berdasarkan nomor surat jalan bongkaran
   resetBySuratJalanBongkaran(['SJB-001', 'SJB-002']);

   // Reset berdasarkan rentang tanggal
   resetByDateRange('2026-01-01', '2026-01-10');
*/
