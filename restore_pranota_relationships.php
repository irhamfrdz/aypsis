<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== RESTORE PRANOTA RELATIONSHIPS ===\n\n";

try {
    DB::beginTransaction();

    // Cari tagihan yang memiliki pranota_id tapi belum ada di tabel pranota
    $tagihanWithPranotaId = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')
        ->where('pranota_id', '>', 0)
        ->get();

    echo "🔍 Ditemukan " . $tagihanWithPranotaId->count() . " tagihan dengan pranota_id\n\n";

    // Group by pranota_id
    $pranotaGroups = [];
    foreach ($tagihanWithPranotaId as $tagihan) {
        $pranotaId = $tagihan->pranota_id;
        if (!isset($pranotaGroups[$pranotaId])) {
            $pranotaGroups[$pranotaId] = [];
        }
        $pranotaGroups[$pranotaId][] = $tagihan;
    }

    echo "📋 Pranota yang perlu di-restore: " . count($pranotaGroups) . "\n\n";

    $restoredCount = 0;
    $skippedCount = 0;

    foreach ($pranotaGroups as $pranotaId => $tagihans) {
        // Cek apakah pranota sudah ada
        $existingPranota = PranotaTagihanKontainerSewa::find($pranotaId);

        if ($existingPranota) {
            echo "⚠️ Pranota ID $pranotaId sudah ada, skip...\n";
            $skippedCount++;
            continue;
        }

        // Hitung total dan buat string tagihan_ids
        $tagihan_ids = [];
        $total_dpp = 0;
        $total_ppn = 0;
        $total_pph = 0;
        $total_grand_total = 0;

        foreach ($tagihans as $tagihan) {
            $tagihan_ids[] = $tagihan->id;
            $total_dpp += $tagihan->dpp;
            $total_ppn += $tagihan->ppn;
            $total_pph += $tagihan->pph;
            $total_grand_total += $tagihan->grand_total;
        }

        // Generate nomor pranota sesuai dengan ID
        $nomorPranota = "PMS11025" . str_pad($pranotaId, 6, '0', STR_PAD_LEFT);

        // Create pranota record
        $pranota = new PranotaTagihanKontainerSewa();
        $pranota->id = $pranotaId; // Set ID eksplisit
        $pranota->nomor_pranota = $nomorPranota;
        $pranota->tagihan_ids = implode(',', $tagihan_ids);
        $pranota->total_dpp = $total_dpp;
        $pranota->total_ppn = $total_ppn;
        $pranota->total_pph = $total_pph;
        $pranota->total_grand_total = $total_grand_total;
        $pranota->status = 'active';
        $pranota->created_at = now();
        $pranota->updated_at = now();

        // Use DB::insert to set explicit ID
        DB::table('pranota_tagihan_kontainer_sewas')->insert([
            'id' => $pranotaId,
            'nomor_pranota' => $nomorPranota,
            'tagihan_ids' => implode(',', $tagihan_ids),
            'total_dpp' => $total_dpp,
            'total_ppn' => $total_ppn,
            'total_pph' => $total_pph,
            'total_grand_total' => $total_grand_total,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ Pranota $nomorPranota (ID: $pranotaId) dibuat dengan " . count($tagihan_ids) . " tagihan\n";
        echo "   💰 Total: Rp " . number_format($total_grand_total, 0, ',', '.') . "\n";

        $restoredCount++;
    }

    DB::commit();

    echo "\n🎉 RESTORE PRANOTA SELESAI!\n";
    echo "==========================\n";
    echo "✅ Pranota yang di-restore: $restoredCount\n";
    echo "⚠️ Pranota yang sudah ada: $skippedCount\n";

} catch (Exception $e) {
    DB::rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

// Verifikasi hasil
echo "\n=== VERIFIKASI HASIL ===\n";

try {
    // Cek pranota yang kosong
    $emptyPranota = PranotaTagihanKontainerSewa::whereRaw("
        LENGTH(tagihan_ids) = 0 OR
        tagihan_ids IS NULL OR
        tagihan_ids = '' OR
        NOT EXISTS (
            SELECT 1 FROM daftar_tagihan_kontainer_sewa
            WHERE FIND_IN_SET(id, pranota_tagihan_kontainer_sewas.tagihan_ids)
        )
    ")->count();

    echo "📊 Pranota kosong: $emptyPranota\n";

    // Total pranota
    $totalPranota = PranotaTagihanKontainerSewa::count();
    echo "📊 Total pranota: $totalPranota\n";

    // Total tagihan
    $totalTagihan = DaftarTagihanKontainerSewa::count();
    echo "📊 Total tagihan: $totalTagihan\n";

    echo "\n✅ Verifikasi selesai!\n";

} catch (Exception $e) {
    echo "❌ Error verifikasi: " . $e->getMessage() . "\n";
}
