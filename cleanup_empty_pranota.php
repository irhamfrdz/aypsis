<?php

/**
 * Script untuk Menghapus Pranota Kosong (Tanpa Kontainer)
 *
 * Script ini akan:
 * 1. Mencari pranota yang tidak memiliki kontainer terkait
 * 2. Menampilkan detail pranota yang akan dihapus
 * 3. Memberikan konfirmasi sebelum penghapusan
 * 4. Menghapus pranota kosong dengan aman
 */

// Pastikan script dijalankan dari direktori Laravel
if (!file_exists('artisan')) {
    die("Error: Script harus dijalankan dari root direktori Laravel\n");
}

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PranotaTagihanKontainerSewa;
use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== SCRIPT CLEANUP PRANOTA KOSONG ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Step 1: Cari pranota yang tidak memiliki kontainer
    echo "🔍 Mencari pranota yang tidak memiliki kontainer...\n";

    $orphanedPranota = DB::select("
        SELECT p.id, p.no_invoice, p.total_amount, p.keterangan, p.tanggal_pranota, p.created_at,
               COUNT(t.id) as kontainer_count
        FROM pranota_tagihan_kontainer_sewa p
        LEFT JOIN daftar_tagihan_kontainer_sewa t ON t.pranota_id = p.id
        GROUP BY p.id, p.no_invoice, p.total_amount, p.keterangan, p.tanggal_pranota, p.created_at
        HAVING COUNT(t.id) = 0
        ORDER BY p.created_at DESC
    ");

    if (empty($orphanedPranota)) {
        echo "✅ Tidak ditemukan pranota kosong. Semua pranota memiliki kontainer.\n";
        exit(0);
    }

    echo "📋 Ditemukan " . count($orphanedPranota) . " pranota kosong:\n\n";

    // Step 2: Tampilkan detail pranota kosong
    $totalAmount = 0;
    foreach ($orphanedPranota as $index => $pranota) {
        echo ($index + 1) . ". No Invoice: {$pranota->no_invoice}\n";
        echo "   Total Amount: Rp " . number_format($pranota->total_amount, 0, ',', '.') . "\n";
        echo "   Keterangan: {$pranota->keterangan}\n";
        echo "   Tanggal: {$pranota->tanggal_pranota}\n";
        echo "   Created: {$pranota->created_at}\n";
        echo "   Kontainer: {$pranota->kontainer_count} (KOSONG)\n";
        echo "\n";

        $totalAmount += $pranota->total_amount;
    }

    echo "💰 Total Amount Pranota Kosong: Rp " . number_format($totalAmount, 0, ',', '.') . "\n\n";

    // Step 3: Konfirmasi penghapusan
    echo "⚠️  PERINGATAN: Script akan menghapus " . count($orphanedPranota) . " pranota kosong.\n";
    echo "❓ Apakah Anda yakin ingin melanjutkan? (ketik 'YA' untuk konfirmasi): ";

    $handle = fopen("php://stdin", "r");
    $confirmation = trim(fgets($handle));
    fclose($handle);

    if (strtoupper($confirmation) !== 'YA') {
        echo "❌ Penghapusan dibatalkan oleh user.\n";
        exit(0);
    }

    echo "\n🗑️  Memulai proses penghapusan...\n";

    // Step 4: Proses penghapusan dengan transaction
    DB::beginTransaction();

    $deletedCount = 0;
    $errors = [];

    foreach ($orphanedPranota as $pranota) {
        try {
            // Double check: Pastikan benar-benar tidak ada kontainer
            $kontainerCount = DaftarTagihanKontainerSewa::where('pranota_id', $pranota->id)->count();

            if ($kontainerCount > 0) {
                $errors[] = "Pranota {$pranota->no_invoice} ternyata memiliki {$kontainerCount} kontainer - SKIP";
                continue;
            }

            // Hapus pranota
            $deleted = PranotaTagihanKontainerSewa::where('id', $pranota->id)->delete();

            if ($deleted) {
                echo "✅ Deleted: {$pranota->no_invoice} (ID: {$pranota->id})\n";
                $deletedCount++;

                // Log penghapusan
                Log::info("Pranota kosong dihapus", [
                    'pranota_id' => $pranota->id,
                    'no_invoice' => $pranota->no_invoice,
                    'total_amount' => $pranota->total_amount,
                    'deleted_by' => 'cleanup_script',
                    'timestamp' => now()
                ]);
            } else {
                $errors[] = "Gagal menghapus pranota {$pranota->no_invoice} (ID: {$pranota->id})";
            }

        } catch (\Exception $e) {
            $errors[] = "Error pada pranota {$pranota->no_invoice}: " . $e->getMessage();
        }
    }

    // Commit transaction jika semua berhasil
    if (empty($errors) || $deletedCount > 0) {
        DB::commit();
        echo "\n✅ Transaction committed successfully.\n";
    } else {
        DB::rollback();
        echo "\n❌ Transaction rolled back due to errors.\n";
    }

    // Step 5: Summary hasil
    echo "\n=== SUMMARY HASIL CLEANUP ===\n";
    echo "📊 Pranota yang berhasil dihapus: {$deletedCount}\n";

    if (!empty($errors)) {
        echo "⚠️  Errors yang terjadi: " . count($errors) . "\n";
        foreach ($errors as $error) {
            echo "   - {$error}\n";
        }
    }

    // Step 6: Verifikasi hasil
    echo "\n🔍 Verifikasi hasil...\n";
    $remainingOrphaned = DB::select("
        SELECT COUNT(*) as count
        FROM pranota_tagihan_kontainer_sewa p
        LEFT JOIN daftar_tagihan_kontainer_sewa t ON t.pranota_id = p.id
        GROUP BY p.id
        HAVING COUNT(t.id) = 0
    ");

    $remainingCount = count($remainingOrphaned);

    if ($remainingCount == 0) {
        echo "✅ Semua pranota kosong berhasil dibersihkan!\n";
    } else {
        echo "⚠️  Masih tersisa {$remainingCount} pranota kosong.\n";
    }

    // Step 7: Database statistics
    echo "\n📈 STATISTIK DATABASE:\n";
    $totalPranota = PranotaTagihanKontainerSewa::count();
    $totalKontainer = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')->count();
    $kontainerBelumPranota = DaftarTagihanKontainerSewa::whereNull('pranota_id')->count();

    echo "   Total Pranota: {$totalPranota}\n";
    echo "   Kontainer dalam Pranota: {$totalKontainer}\n";
    echo "   Kontainer belum Pranota: {$kontainerBelumPranota}\n";

    echo "\n🎉 Cleanup selesai!\n";

} catch (\Exception $e) {
    if (DB::transactionLevel() > 0) {
        DB::rollback();
    }

    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";

    Log::error("Cleanup pranota kosong gagal", [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);

    exit(1);
}
