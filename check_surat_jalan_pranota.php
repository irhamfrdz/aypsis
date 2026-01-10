<?php
/**
 * Script untuk mengecek kenapa surat jalan yang muncul di pranota sedikit
 * 
 * Usage:
 * php artisan tinker
 * include 'check_surat_jalan_pranota.php';
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
echo "║       ANALISIS SURAT JALAN PRANOTA UANG RIT                   ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$handle = fopen("php://stdin", "r");

echo "Masukkan tanggal mulai (YYYY-MM-DD, contoh: 2026-01-01): ";
$startDate = trim(fgets($handle));

echo "Masukkan tanggal akhir (YYYY-MM-DD, contoh: 2026-01-10): ";
$endDate = trim(fgets($handle));

fclose($handle);

if (empty($startDate) || empty($endDate)) {
    echo "❌ Tanggal tidak boleh kosong!\n";
    exit;
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "  ANALISIS PERIODE: {$startDate} - {$endDate}\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";

// 1. Total surat jalan di database
$totalSuratJalan = DB::table('surat_jalans')->count();
echo "📋 1. TOTAL SURAT JALAN DI DATABASE\n";
echo "   Total: {$totalSuratJalan}\n\n";

// 2. Surat jalan yang menggunakan rit
$menggunakanRit = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->count();
echo "🚗 2. SURAT JALAN YANG MENGGUNAKAN RIT\n";
echo "   Total: {$menggunakanRit}\n";
echo "   Yang tidak menggunakan rit: " . ($totalSuratJalan - $menggunakanRit) . "\n\n";

// 3. Status pembayaran uang rit
echo "💰 3. STATUS PEMBAYARAN UANG RIT\n";
$statusPembayaran = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->select('status_pembayaran_uang_rit', DB::raw('count(*) as total'))
    ->groupBy('status_pembayaran_uang_rit')
    ->get();

foreach ($statusPembayaran as $status) {
    $statusText = $status->status_pembayaran_uang_rit ?: 'NULL';
    echo "   - {$statusText}: {$status->total}\n";
}
echo "\n";

// 4. Surat jalan yang belum dibayar dan menggunakan rit
$belumDibayar = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->count();
echo "📊 4. SURAT JALAN MENGGUNAKAN RIT & BELUM DIBAYAR\n";
echo "   Total: {$belumDibayar}\n\n";

// 5. Yang sudah ada di pranota
$sudahPranota = DB::table('pranota_uang_rits')
    ->whereNotNull('surat_jalan_id')
    ->whereNotIn('status', ['cancelled'])
    ->distinct('surat_jalan_id')
    ->count();
echo "📝 5. SURAT JALAN YANG SUDAH ADA DI PRANOTA (TIDAK CANCELLED)\n";
echo "   Total: {$sudahPranota}\n\n";

// 6. Surat jalan dengan checkpoint
$adaCheckpoint = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->whereNotNull('tanggal_checkpoint')
    ->count();
echo "✅ 6. SURAT JALAN DENGAN CHECKPOINT (rit + belum dibayar)\n";
echo "   Total: {$adaCheckpoint}\n\n";

// 7. Surat jalan dengan checkpoint dalam rentang tanggal
$checkpointDalamRentang = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->whereNotNull('tanggal_checkpoint')
    ->whereRaw('DATE(tanggal_checkpoint) >= ?', [$startDate])
    ->whereRaw('DATE(tanggal_checkpoint) <= ?', [$endDate])
    ->count();
echo "📅 7. CHECKPOINT DALAM RENTANG TANGGAL {$startDate} - {$endDate}\n";
echo "   Total: {$checkpointDalamRentang}\n\n";

// 8. Surat jalan dengan tanda terima
$adaTandaTerima = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
              ->from('tanda_terimas')
              ->whereColumn('tanda_terimas.surat_jalan_id', 'surat_jalans.id');
    })
    ->count();
echo "📄 8. SURAT JALAN DENGAN TANDA TERIMA (rit + belum dibayar)\n";
echo "   Total: {$adaTandaTerima}\n\n";

// 9. Tanda terima dalam rentang tanggal
$tandaTerimaDalamRentang = DB::table('surat_jalans')
    ->where('surat_jalans.rit', 'menggunakan_rit')
    ->where('surat_jalans.status_pembayaran_uang_rit', 'belum_dibayar')
    ->join('tanda_terimas', 'surat_jalans.id', '=', 'tanda_terimas.surat_jalan_id')
    ->whereRaw('DATE(tanda_terimas.tanggal) >= ?', [$startDate])
    ->whereRaw('DATE(tanda_terimas.tanggal) <= ?', [$endDate])
    ->count();
echo "📅 9. TANDA TERIMA DALAM RENTANG TANGGAL {$startDate} - {$endDate}\n";
echo "   Total: {$tandaTerimaDalamRentang}\n\n";

// 10. Status surat jalan
echo "📌 10. STATUS SURAT JALAN (yang rit + belum dibayar)\n";
$statusSJ = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->select('status', DB::raw('count(*) as total'))
    ->groupBy('status')
    ->get();

foreach ($statusSJ as $status) {
    $statusText = $status->status ?: 'NULL';
    echo "   - {$statusText}: {$status->total}\n";
}
echo "\n";

// 11. Final: yang memenuhi semua syarat tapi belum pranota
$memenuhi = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->whereNotIn('id', function($query) {
        $query->select('surat_jalan_id')
            ->from('pranota_uang_rits')
            ->whereNotNull('surat_jalan_id')
            ->whereNotIn('status', ['cancelled']);
    })
    ->where(function($q) use ($startDate, $endDate) {
        $q->where(function($subQ) use ($startDate, $endDate) {
            // Checkpoint dalam rentang
            $subQ->whereNotNull('tanggal_checkpoint')
                 ->whereRaw('DATE(tanggal_checkpoint) >= ?', [$startDate])
                 ->whereRaw('DATE(tanggal_checkpoint) <= ?', [$endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
            // Tanda terima dalam rentang
            $subQ->whereExists(function($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                      ->from('tanda_terimas')
                      ->whereColumn('tanda_terimas.surat_jalan_id', 'surat_jalans.id')
                      ->whereRaw('DATE(tanda_terimas.tanggal) >= ?', [$startDate])
                      ->whereRaw('DATE(tanda_terimas.tanggal) <= ?', [$endDate]);
            });
        });
    })
    ->count();

echo "✨ 11. YANG MEMENUHI SEMUA SYARAT & MUNCUL DI FORM\n";
echo "   Total: {$memenuhi}\n\n";

// 12. Contoh surat jalan yang memenuhi syarat
echo "📋 12. CONTOH SURAT JALAN YANG MEMENUHI SYARAT (5 teratas)\n";
$examples = DB::table('surat_jalans')
    ->where('rit', 'menggunakan_rit')
    ->where('status_pembayaran_uang_rit', 'belum_dibayar')
    ->whereNotIn('id', function($query) {
        $query->select('surat_jalan_id')
            ->from('pranota_uang_rits')
            ->whereNotNull('surat_jalan_id')
            ->whereNotIn('status', ['cancelled']);
    })
    ->where(function($q) use ($startDate, $endDate) {
        $q->where(function($subQ) use ($startDate, $endDate) {
            $subQ->whereNotNull('tanggal_checkpoint')
                 ->whereRaw('DATE(tanggal_checkpoint) >= ?', [$startDate])
                 ->whereRaw('DATE(tanggal_checkpoint) <= ?', [$endDate]);
        })
        ->orWhere(function($subQ) use ($startDate, $endDate) {
            $subQ->whereExists(function($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                      ->from('tanda_terimas')
                      ->whereColumn('tanda_terimas.surat_jalan_id', 'surat_jalans.id')
                      ->whereRaw('DATE(tanda_terimas.tanggal) >= ?', [$startDate])
                      ->whereRaw('DATE(tanda_terimas.tanggal) <= ?', [$endDate]);
            });
        });
    })
    ->select('id', 'no_surat_jalan', 'tanggal_surat_jalan', 'supir', 'tanggal_checkpoint', 'status', 'status_pembayaran_uang_rit')
    ->limit(5)
    ->get();

foreach ($examples as $ex) {
    echo "   - ID: {$ex->id} | No: {$ex->no_surat_jalan} | Supir: {$ex->supir}\n";
    echo "     Tgl SJ: {$ex->tanggal_surat_jalan} | Checkpoint: {$ex->tanggal_checkpoint}\n";
    echo "     Status: {$ex->status} | Pembayaran: {$ex->status_pembayaran_uang_rit}\n\n";
}

// 13. Yang TIDAK memenuhi syarat dan kenapa
echo "❌ 13. KENAPA SURAT JALAN TIDAK MUNCUL?\n\n";

$tidakRit = $totalSuratJalan - $menggunakanRit;
$sudahDibayar = $menggunakanRit - $belumDibayar;
$checkpointDiluarRentang = $adaCheckpoint - $checkpointDalamRentang;
$tandaTerimaDiluarRentang = $adaTandaTerima - $tandaTerimaDalamRentang;

echo "   ❌ {$tidakRit} - Tidak menggunakan rit (rit = 'tidak_menggunakan_rit')\n";
echo "   ❌ {$sudahDibayar} - Sudah dibayar (status_pembayaran_uang_rit != 'belum_dibayar')\n";
echo "   ❌ {$sudahPranota} - Sudah ada di pranota (sudah diproses sebelumnya)\n";
echo "   ❌ {$checkpointDiluarRentang} - Checkpoint di luar rentang tanggal\n";
echo "   ❌ {$tandaTerimaDiluarRentang} - Tanda terima di luar rentang tanggal\n";

$belumCheckpoint = $belumDibayar - $adaCheckpoint;
echo "   ❌ {$belumCheckpoint} - Belum checkpoint sama sekali\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "  KESIMPULAN\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";
echo "Yang muncul di form: {$memenuhi} surat jalan\n";
echo "\n";
echo "Alasan utama kenapa sedikit:\n";
echo "1. Filter berdasarkan TANGGAL CHECKPOINT/TANDA TERIMA, bukan tanggal surat jalan\n";
echo "2. Harus sudah 'menggunakan_rit'\n";
echo "3. Status pembayaran harus 'belum_dibayar'\n";
echo "4. Belum pernah masuk pranota sebelumnya\n";
echo "5. Harus sudah checkpoint atau ada tanda terima dalam rentang {$startDate} - {$endDate}\n";
echo "\n";

// Saran
if ($memenuhi < 10) {
    echo "💡 SARAN:\n";
    echo "   - Cek apakah surat jalan sudah di-checkpoint?\n";
    echo "   - Cek apakah tanggal checkpoint sesuai dengan rentang yang dipilih?\n";
    echo "   - Cek apakah status pembayaran sudah diset 'belum_dibayar'?\n";
    echo "   - Jika perlu filter berdasarkan tanggal surat jalan, hubungi developer\n";
    echo "\n";
}

echo "\n";
