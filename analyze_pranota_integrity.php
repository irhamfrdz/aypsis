<?php

/**
 * Script Analisis Pranota Kosong (Dry Run)
 *
 * Script ini akan menganalisis pranota yang tidak memiliki kontainer
 * tanpa menghapus apapun - hanya untuk analisis dan review.
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

echo "=== ANALISIS PRANOTA KOSONG (DRY RUN) ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Statistik umum database
    echo "📊 STATISTIK DATABASE:\n";
    $totalPranota = PranotaTagihanKontainerSewa::count();
    $totalKontainer = DaftarTagihanKontainerSewa::count();
    $kontainerDenganPranota = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')->count();
    $kontainerTanpaPranota = DaftarTagihanKontainerSewa::whereNull('pranota_id')->count();

    echo "   Total Pranota: {$totalPranota}\n";
    echo "   Total Kontainer: {$totalKontainer}\n";
    echo "   Kontainer dengan Pranota: {$kontainerDenganPranota}\n";
    echo "   Kontainer tanpa Pranota: {$kontainerTanpaPranota}\n\n";

    // Analisis pranota berdasarkan jumlah kontainer
    echo "🔍 ANALISIS PRANOTA BERDASARKAN JUMLAH KONTAINER:\n";
    $pranotaStats = DB::select("
        SELECT
            CASE
                WHEN COUNT(t.id) = 0 THEN 'Kosong (0 kontainer)'
                WHEN COUNT(t.id) = 1 THEN '1 kontainer'
                WHEN COUNT(t.id) BETWEEN 2 AND 5 THEN '2-5 kontainer'
                WHEN COUNT(t.id) BETWEEN 6 AND 10 THEN '6-10 kontainer'
                ELSE 'Lebih dari 10 kontainer'
            END as kategori,
            COUNT(p.id) as jumlah_pranota,
            SUM(p.total_amount) as total_amount
        FROM pranota_tagihan_kontainer_sewa p
        LEFT JOIN daftar_tagihan_kontainer_sewa t ON t.pranota_id = p.id
        GROUP BY p.id, p.total_amount
    ");

    // Group statistics
    $stats = [];
    foreach ($pranotaStats as $stat) {
        $kategori = $stat->kategori ?? 'Unknown';
        if (!isset($stats[$kategori])) {
            $stats[$kategori] = ['count' => 0, 'amount' => 0];
        }
        $stats[$kategori]['count']++;
        $stats[$kategori]['amount'] += $stat->total_amount ?? 0;
    }

    foreach ($stats as $kategori => $data) {
        echo "   {$kategori}: {$data['count']} pranota (Rp " . number_format($data['amount'], 0, ',', '.') . ")\n";
    }

    // Cari pranota kosong detail
    echo "\n🔍 DETAIL PRANOTA KOSONG:\n";
    $orphanedPranota = DB::select("
        SELECT p.id, p.no_invoice, p.total_amount, p.keterangan, p.tanggal_pranota,
               p.created_at, p.status, p.jumlah_tagihan,
               COUNT(t.id) as kontainer_count,
               DATEDIFF(NOW(), p.created_at) as days_old
        FROM pranota_tagihan_kontainer_sewa p
        LEFT JOIN daftar_tagihan_kontainer_sewa t ON t.pranota_id = p.id
        GROUP BY p.id, p.no_invoice, p.total_amount, p.keterangan, p.tanggal_pranota,
                 p.created_at, p.status, p.jumlah_tagihan
        HAVING COUNT(t.id) = 0
        ORDER BY p.created_at DESC
    ");

    if (empty($orphanedPranota)) {
        echo "✅ Tidak ditemukan pranota kosong!\n\n";
    } else {
        echo "❌ Ditemukan " . count($orphanedPranota) . " pranota kosong:\n\n";

        $totalAmountOrphaned = 0;
        $oldestDate = null;
        $newestDate = null;

        foreach ($orphanedPranota as $index => $pranota) {
            echo ($index + 1) . ". No Invoice: {$pranota->no_invoice}\n";
            echo "   ID: {$pranota->id}\n";
            echo "   Total Amount: Rp " . number_format($pranota->total_amount, 0, ',', '.') . "\n";
            echo "   Status: {$pranota->status}\n";
            echo "   Jumlah Tagihan (claimed): {$pranota->jumlah_tagihan}\n";
            echo "   Kontainer (actual): {$pranota->kontainer_count}\n";
            echo "   Keterangan: " . (strlen($pranota->keterangan) > 50 ? substr($pranota->keterangan, 0, 50) . '...' : $pranota->keterangan) . "\n";
            echo "   Tanggal: {$pranota->tanggal_pranota}\n";
            echo "   Created: {$pranota->created_at} ({$pranota->days_old} hari yang lalu)\n";
            echo "\n";

            $totalAmountOrphaned += $pranota->total_amount;

            if ($oldestDate === null || $pranota->created_at < $oldestDate) {
                $oldestDate = $pranota->created_at;
            }
            if ($newestDate === null || $pranota->created_at > $newestDate) {
                $newestDate = $pranota->created_at;
            }
        }

        echo "💰 Total Amount Pranota Kosong: Rp " . number_format($totalAmountOrphaned, 0, ',', '.') . "\n";
        echo "📅 Rentang Tanggal: {$oldestDate} s/d {$newestDate}\n\n";

        // Analisis berdasarkan umur
        echo "📈 ANALISIS BERDASARKAN UMUR:\n";
        $ageAnalysis = [];
        foreach ($orphanedPranota as $pranota) {
            $days = $pranota->days_old;
            if ($days <= 7) {
                $category = '≤ 7 hari';
            } elseif ($days <= 30) {
                $category = '8-30 hari';
            } elseif ($days <= 90) {
                $category = '31-90 hari';
            } else {
                $category = '> 90 hari';
            }

            if (!isset($ageAnalysis[$category])) {
                $ageAnalysis[$category] = ['count' => 0, 'amount' => 0];
            }
            $ageAnalysis[$category]['count']++;
            $ageAnalysis[$category]['amount'] += $pranota->total_amount;
        }

        foreach ($ageAnalysis as $category => $data) {
            echo "   {$category}: {$data['count']} pranota (Rp " . number_format($data['amount'], 0, ',', '.') . ")\n";
        }

        // Analisis berdasarkan status
        echo "\n📊 ANALISIS BERDASARKAN STATUS:\n";
        $statusAnalysis = [];
        foreach ($orphanedPranota as $pranota) {
            $status = $pranota->status ?? 'unknown';
            if (!isset($statusAnalysis[$status])) {
                $statusAnalysis[$status] = ['count' => 0, 'amount' => 0];
            }
            $statusAnalysis[$status]['count']++;
            $statusAnalysis[$status]['amount'] += $pranota->total_amount;
        }

        foreach ($statusAnalysis as $status => $data) {
            echo "   {$status}: {$data['count']} pranota (Rp " . number_format($data['amount'], 0, ',', '.') . ")\n";
        }
    }

    // Cek konsistensi data
    echo "\n🔍 CEK KONSISTENSI DATA:\n";

    // Pranota dengan claimed jumlah != actual kontainer
    $inconsistentPranota = DB::select("
        SELECT p.id, p.no_invoice, p.jumlah_tagihan, COUNT(t.id) as actual_count
        FROM pranota_tagihan_kontainer_sewa p
        LEFT JOIN daftar_tagihan_kontainer_sewa t ON t.pranota_id = p.id
        GROUP BY p.id, p.no_invoice, p.jumlah_tagihan
        HAVING p.jumlah_tagihan != COUNT(t.id)
        ORDER BY p.id
    ");

    if (!empty($inconsistentPranota)) {
        echo "⚠️  Ditemukan " . count($inconsistentPranota) . " pranota dengan jumlah kontainer tidak konsisten:\n";
        foreach ($inconsistentPranota as $pranota) {
            echo "   {$pranota->no_invoice}: Claimed={$pranota->jumlah_tagihan}, Actual={$pranota->actual_count}\n";
        }
    } else {
        echo "✅ Semua pranota konsisten dengan jumlah kontainer\n";
    }

    // Kontainer dengan pranota_id yang tidak valid
    $invalidKontainer = DB::select("
        SELECT t.id, t.nomor_kontainer, t.pranota_id
        FROM daftar_tagihan_kontainer_sewa t
        LEFT JOIN pranota_tagihan_kontainer_sewa p ON p.id = t.pranota_id
        WHERE t.pranota_id IS NOT NULL AND p.id IS NULL
    ");

    if (!empty($invalidKontainer)) {
        echo "\n⚠️  Ditemukan " . count($invalidKontainer) . " kontainer dengan pranota_id tidak valid:\n";
        foreach ($invalidKontainer as $kontainer) {
            echo "   {$kontainer->nomor_kontainer}: pranota_id={$kontainer->pranota_id} (tidak exists)\n";
        }
    } else {
        echo "\n✅ Semua kontainer dengan pranota_id valid\n";
    }

    echo "\n=== REKOMENDASI ===\n";

    if (empty($orphanedPranota)) {
        echo "✅ Database dalam kondisi baik, tidak ada pranota kosong\n";
    } else {
        echo "🗑️  Disarankan untuk menghapus " . count($orphanedPranota) . " pranota kosong\n";
        echo "💰 Total amount yang akan dibersihkan: Rp " . number_format($totalAmountOrphaned, 0, ',', '.') . "\n";
        echo "📝 Gunakan script 'cleanup_empty_pranota.php' untuk melakukan penghapusan\n";
    }

    if (!empty($inconsistentPranota)) {
        echo "⚠️  Ada " . count($inconsistentPranota) . " pranota dengan data tidak konsisten yang perlu direview\n";
    }

    if (!empty($invalidKontainer)) {
        echo "🔧 Ada " . count($invalidKontainer) . " kontainer dengan pranota_id invalid yang perlu diperbaiki\n";
    }

    echo "\n✅ Analisis selesai!\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
