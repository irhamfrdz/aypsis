<?php
/**
 * Script untuk update status surat jalan agar muncul di pranota
 * 
 * Usage:
 * php update_status_surat_jalan.php
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
echo "║       UPDATE STATUS SURAT JALAN UNTUK PRANOTA UANG RIT        ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$handle = fopen("php://stdin", "r");

echo "Pilih metode update:\n";
echo "1. Update tanggal checkpoint surat jalan (berdasarkan tanggal SJ)\n";
echo "2. Update status pembayaran ke 'belum_dibayar'\n";
echo "3. Update keduanya (checkpoint + status pembayaran)\n";
echo "\n";
echo "Pilihan Anda: ";
$choice = trim(fgets($handle));

if (!in_array($choice, ['1', '2', '3'])) {
    echo "❌ Pilihan tidak valid!\n";
    fclose($handle);
    exit;
}

echo "\nMasukkan tanggal surat jalan MULAI (YYYY-MM-DD, contoh: 2026-01-01): ";
$startDate = trim(fgets($handle));

echo "Masukkan tanggal surat jalan AKHIR (YYYY-MM-DD, contoh: 2026-01-10): ";
$endDate = trim(fgets($handle));

if (empty($startDate) || empty($endDate)) {
    echo "❌ Tanggal tidak boleh kosong!\n";
    fclose($handle);
    exit;
}

$newCheckpointDate = null;
if (in_array($choice, ['1', '3'])) {
    echo "\nMasukkan tanggal checkpoint BARU (YYYY-MM-DD HH:MM:SS atau YYYY-MM-DD): ";
    $newCheckpointDate = trim(fgets($handle));
    
    if (empty($newCheckpointDate)) {
        echo "❌ Tanggal checkpoint tidak boleh kosong!\n";
        fclose($handle);
        exit;
    }
    
    // Add time if not provided
    if (strlen($newCheckpointDate) == 10) {
        $newCheckpointDate .= ' 07:00:00';
    }
}

fclose($handle);

try {
    DB::beginTransaction();
    
    echo "\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  PROSES UPDATE\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "\n";
    
    // Query surat jalan yang akan diupdate
    $query = DB::table('surat_jalans')
        ->where('rit', 'menggunakan_rit')
        ->whereRaw('DATE(tanggal_surat_jalan) >= ?', [$startDate])
        ->whereRaw('DATE(tanggal_surat_jalan) <= ?', [$endDate]);
    
    // Get count dan sample
    $count = $query->count();
    $samples = $query->select('id', 'no_surat_jalan', 'tanggal_surat_jalan', 'supir', 'tanggal_checkpoint', 'status_pembayaran_uang_rit')
        ->limit(5)
        ->get();
    
    if ($count == 0) {
        echo "❌ Tidak ada surat jalan yang ditemukan dalam rentang tanggal tersebut.\n";
        DB::rollBack();
        exit;
    }
    
    echo "🔍 Ditemukan {$count} surat jalan dalam rentang tanggal {$startDate} - {$endDate}\n\n";
    
    echo "📋 Contoh surat jalan yang akan diupdate (5 teratas):\n";
    foreach ($samples as $s) {
        echo "   - ID: {$s->id} | No: {$s->no_surat_jalan} | Supir: {$s->supir}\n";
        echo "     Checkpoint lama: " . ($s->tanggal_checkpoint ?: 'NULL') . "\n";
        echo "     Status pembayaran lama: " . ($s->status_pembayaran_uang_rit ?: 'NULL') . "\n\n";
    }
    
    echo "⚠️  PERINGATAN: Anda akan mengupdate {$count} surat jalan!\n";
    echo "Update yang akan dilakukan:\n";
    
    if (in_array($choice, ['1', '3'])) {
        echo "   - Tanggal checkpoint → {$newCheckpointDate}\n";
    }
    if (in_array($choice, ['2', '3'])) {
        echo "   - Status pembayaran → belum_dibayar\n";
    }
    
    echo "\n";
    echo "Lanjutkan? (yes/no): ";
    $confirm = trim(fgets(STDIN));
    
    if (strtolower($confirm) !== 'yes') {
        echo "❌ Update dibatalkan.\n";
        DB::rollBack();
        exit;
    }
    
    // Prepare update data
    $updateData = [
        'updated_at' => now()
    ];
    
    if (in_array($choice, ['1', '3'])) {
        $updateData['tanggal_checkpoint'] = $newCheckpointDate;
        $updateData['status'] = 'sudah_checkpoint';
    }
    
    if (in_array($choice, ['2', '3'])) {
        $updateData['status_pembayaran_uang_rit'] = 'belum_dibayar';
    }
    
    // Execute update
    $updated = DB::table('surat_jalans')
        ->where('rit', 'menggunakan_rit')
        ->whereRaw('DATE(tanggal_surat_jalan) >= ?', [$startDate])
        ->whereRaw('DATE(tanggal_surat_jalan) <= ?', [$endDate])
        ->update($updateData);
    
    echo "\n✅ Berhasil update {$updated} surat jalan!\n\n";
    
    // Show updated data
    $updatedSamples = DB::table('surat_jalans')
        ->where('rit', 'menggunakan_rit')
        ->whereRaw('DATE(tanggal_surat_jalan) >= ?', [$startDate])
        ->whereRaw('DATE(tanggal_surat_jalan) <= ?', [$endDate])
        ->select('id', 'no_surat_jalan', 'supir', 'tanggal_checkpoint', 'status_pembayaran_uang_rit', 'status')
        ->limit(5)
        ->get();
    
    echo "📋 Contoh hasil update (5 teratas):\n";
    foreach ($updatedSamples as $s) {
        echo "   - ID: {$s->id} | No: {$s->no_surat_jalan} | Supir: {$s->supir}\n";
        echo "     Checkpoint baru: " . ($s->tanggal_checkpoint ?: 'NULL') . "\n";
        echo "     Status pembayaran baru: " . ($s->status_pembayaran_uang_rit ?: 'NULL') . "\n";
        echo "     Status: " . ($s->status ?: 'NULL') . "\n\n";
    }
    
    DB::commit();
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  SELESAI\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "\n";
    echo "✨ Surat jalan sekarang akan muncul di form pranota!\n";
    echo "   (dengan asumsi tanggal checkpoint dalam rentang yang dipilih)\n";
    echo "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
