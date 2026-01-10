<?php
/**
 * Script untuk mengubah status pembayaran uang rit menjadi 'belum_dibayar'
 * 
 * Usage:
 * php update_status_pembayaran_uang_rit.php
 */

// Jika dijalankan langsung (bukan dari tinker)
if (php_sapi_name() === 'cli' && !class_exists('DB')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
}

use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     UPDATE STATUS PEMBAYARAN UANG RIT KE 'BELUM_DIBAYAR'     ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    DB::beginTransaction();
    
    // Cek jumlah surat jalan REGULAR dengan status bukan 'belum_dibayar'
    $sudahDibayar = DB::table('surat_jalans')
        ->where('rit', 'menggunakan_rit')
        ->where(function($q) {
            $q->where('status_pembayaran_uang_rit', '!=', 'belum_dibayar')
              ->orWhereNull('status_pembayaran_uang_rit');
        })
        ->get();
    
    $count = $sudahDibayar->count();
    
    // Cek jumlah surat jalan BONGKARAN dengan status bukan 'belum_bayar'
    $sudahDibayarBongkaran = DB::table('surat_jalan_bongkarans')
        ->where('rit', 'menggunakan_rit')
        ->where(function($q) {
            $q->where('status_pembayaran_uang_rit', '!=', 'belum_bayar')
              ->orWhereNull('status_pembayaran_uang_rit');
        })
        ->get();
    
    $countBongkaran = $sudahDibayarBongkaran->count();
    $totalCount = $count + $countBongkaran;
    $totalCount = $count + $countBongkaran;
    
    if ($totalCount == 0) {
        echo "✅ Semua surat jalan (regular & bongkaran) sudah berstatus 'belum_dibayar'.\n";
        echo "   Tidak ada yang perlu diupdate.\n\n";
        DB::rollBack();
        exit;
    }
    
    echo "🔍 SURAT JALAN REGULAR:\n";
    echo "   Ditemukan {$count} surat jalan dengan status pembayaran bukan 'belum_dibayar'\n\n";
    
    echo "🔍 SURAT JALAN BONGKARAN:\n";
    echo "   Ditemukan {$countBongkaran} surat jalan bongkaran dengan status pembayaran bukan 'belum_bayar'\n\n";
    
    echo "📊 TOTAL: {$totalCount} surat jalan akan diupdate\n\n";
    
    // Group by status - REGULAR
    if ($count > 0) {
        $grouped = $sudahDibayar->groupBy('status_pembayaran_uang_rit');
        
        echo "📊 Rincian status pembayaran REGULAR saat ini:\n";
        foreach ($grouped as $status => $items) {
            $statusText = $status ?: 'NULL';
            echo "   - {$statusText}: " . $items->count() . " surat jalan\n";
        }
        echo "\n";
    }
    
    // Group by status - BONGKARAN
    if ($countBongkaran > 0) {
        $groupedBongkaran = $sudahDibayarBongkaran->groupBy('status_pembayaran_uang_rit');
        
        echo "📊 Rincian status pembayaran BONGKARAN saat ini:\n";
        foreach ($groupedBongkaran as $status => $items) {
            $statusText = $status ?: 'NULL';
            echo "   - {$statusText}: " . $items->count() . " surat jalan bongkaran\n";
        }
        echo "\n";
    }
    
    // Show samples - REGULAR
    if ($count > 0) {
        echo "📋 Contoh surat jalan REGULAR yang akan diupdate (5 teratas):\n";
        $samples = $sudahDibayar->take(5);
        foreach ($samples as $s) {
            echo "   - ID: {$s->id} | No: {$s->no_surat_jalan} | Supir: {$s->supir}\n";
            echo "     Tgl SJ: {$s->tanggal_surat_jalan} | Status lama: " . ($s->status_pembayaran_uang_rit ?: 'NULL') . "\n\n";
        }
    }
    
    // Show samples - BONGKARAN
    if ($countBongkaran > 0) {
        echo "📋 Contoh surat jalan BONGKARAN yang akan diupdate (5 teratas):\n";
        $samplesBongkaran = $sudahDibayarBongkaran->take(5);
        foreach ($samplesBongkaran as $s) {
            echo "   - ID: {$s->id} | No: {$s->nomor_surat_jalan} | Supir: {$s->supir}\n";
            echo "     Tgl SJ: {$s->tanggal_surat_jalan} | Status lama: " . ($s->status_pembayaran_uang_rit ?: 'NULL') . "\n\n";
        }
    }
    
    echo "⚠️  PERINGATAN: Anda akan mengubah status pembayaran {$totalCount} surat jalan!\n";
    echo "   - {$count} surat jalan regular → 'belum_dibayar'\n";
    echo "   - {$countBongkaran} surat jalan bongkaran → 'belum_bayar'\n\n";
    
    echo "Lanjutkan? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $confirm = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirm) !== 'yes') {
        echo "❌ Update dibatalkan.\n";
        DB::rollBack();
        exit;
    }
    
    // Execute update - REGULAR
    $updated = 0;
    if ($count > 0) {
        $updated = DB::table('surat_jalans')
            ->where('rit', '=', 'menggunakan_rit')
            ->where(function($q) {
                $q->where('status_pembayaran_uang_rit', '!=', 'belum_dibayar')
                  ->orWhereNull('status_pembayaran_uang_rit');
            })
            ->update([
                'status_pembayaran_uang_rit' => 'belum_dibayar',
                'updated_at' => DB::raw('NOW()')
            ]);
    }
    
    // Execute update - BONGKARAN
    $updatedBongkaran = 0;
    if ($countBongkaran > 0) {
        $updatedBongkaran = DB::table('surat_jalan_bongkarans')
            ->where('rit', '=', 'menggunakan_rit')
            ->where(function($q) {
                $q->where('status_pembayaran_uang_rit', '!=', 'belum_bayar')
                  ->orWhereNull('status_pembayaran_uang_rit');
            })
            ->update([
                'status_pembayaran_uang_rit' => 'belum_bayar',
                'updated_at' => DB::raw('NOW()')
            ]);
    }
    
    $totalUpdated = $updated + $updatedBongkaran;
    
    echo "\n✅ Berhasil update {$totalUpdated} surat jalan!\n";
    echo "   - {$updated} surat jalan regular\n";
    echo "   - {$updatedBongkaran} surat jalan bongkaran\n\n";
    
    // Show updated data - REGULAR
    $afterUpdate = DB::table('surat_jalans')
        ->where('rit', 'menggunakan_rit')
        ->select('status_pembayaran_uang_rit', DB::raw('count(*) as total'))
        ->groupBy('status_pembayaran_uang_rit')
        ->get();
    
    echo "📊 Status pembayaran REGULAR setelah update:\n";
    foreach ($afterUpdate as $status) {
        $statusText = $status->status_pembayaran_uang_rit ?: 'NULL';
        echo "   - {$statusText}: {$status->total}\n";
    }
    echo "\n";
    
    // Show updated data - BONGKARAN
    $afterUpdateBongkaran = DB::table('surat_jalan_bongkarans')
        ->where('rit', 'menggunakan_rit')
        ->select('status_pembayaran_uang_rit', DB::raw('count(*) as total'))
        ->groupBy('status_pembayaran_uang_rit')
        ->get();
    
    echo "📊 Status pembayaran BONGKARAN setelah update:\n";
    foreach ($afterUpdateBongkaran as $status) {
        $statusText = $status->status_pembayaran_uang_rit ?: 'NULL';
        echo "   - {$statusText}: {$status->total}\n";
    }
    echo "\n";
    
    DB::commit();
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  SELESAI\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "\n";
    echo "✨ Surat jalan sekarang akan muncul di form pranota!\n";
    echo "   (dengan syarat sudah checkpoint/tanda terima dalam rentang tanggal)\n";
    echo "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
