<?php

/**
 * Script untuk mengubah Grand Total kontainer DNAU2622206 periode 4
 * Mengubah nilai menjadi 294.604,00
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

try {
    echo "========================================\n";
    echo "Update Grand Total Kontainer DNAU2622206\n";
    echo "========================================\n\n";

    // Cari tagihan dengan nomor kontainer DNAU2622206 dan periode 4
    $tagihan = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'DNAU2622206')
        ->where('periode', '4')
        ->first();

    if (!$tagihan) {
        echo "❌ Tagihan tidak ditemukan!\n";
        echo "Nomor Kontainer: DNAU2622206\n";
        echo "Periode: 4\n";
        exit(1);
    }

    echo "✓ Tagihan ditemukan:\n";
    echo "  ID: {$tagihan->id}\n";
    echo "  Nomor Kontainer: {$tagihan->nomor_kontainer}\n";
    echo "  Vendor: {$tagihan->vendor}\n";
    echo "  Periode: {$tagihan->periode}\n";
    echo "  Masa: {$tagihan->masa}\n";
    echo "  Size: {$tagihan->size}\n";
    echo "  Grand Total Lama: Rp " . number_format($tagihan->grand_total, 2, ',', '.') . "\n";
    echo "\n";

    // Nilai grand total baru
    $newGrandTotal = 294604.00;

    echo "Grand Total Baru: Rp " . number_format($newGrandTotal, 2, ',', '.') . "\n";
    echo "\n";

    // Konfirmasi
    echo "Apakah Anda yakin ingin mengubah grand total? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $confirmation = trim(strtolower($line));
    fclose($handle);

    if ($confirmation !== 'yes') {
        echo "\n❌ Operasi dibatalkan!\n";
        exit(0);
    }

    echo "\n";
    echo "Memproses update...\n";

    // Update dalam transaction
    DB::beginTransaction();

    try {
        $oldGrandTotal = $tagihan->grand_total;

        // Update grand total directly without triggering model events
        DB::table('daftar_tagihan_kontainer_sewa')
            ->where('id', $tagihan->id)
            ->update([
                'grand_total' => $newGrandTotal,
                'updated_at' => now()
            ]);

        DB::commit();

        echo "\n✅ Berhasil mengubah Grand Total!\n";
        echo "  Nilai Lama: Rp " . number_format($oldGrandTotal, 2, ',', '.') . "\n";
        echo "  Nilai Baru: Rp " . number_format($newGrandTotal, 2, ',', '.') . "\n";
        echo "  Selisih: Rp " . number_format($newGrandTotal - $oldGrandTotal, 2, ',', '.') . "\n";
        echo "\n";

        // Refresh data from database
        $tagihan->refresh();

        // Tampilkan ringkasan tagihan setelah update
        echo "Ringkasan Tagihan Setelah Update:\n";
        echo "  DPP: Rp " . number_format($tagihan->dpp ?? 0, 2, ',', '.') . "\n";
        echo "  PPN: Rp " . number_format($tagihan->ppn ?? 0, 2, ',', '.') . "\n";
        echo "  Adjustment: Rp " . number_format($tagihan->adjustment ?? 0, 2, ',', '.') . "\n";
        echo "  Grand Total: Rp " . number_format($tagihan->grand_total, 2, ',', '.') . "\n";

        if ($tagihan->adjustment_note) {
            echo "  Catatan Adjustment: {$tagihan->adjustment_note}\n";
        }

        echo "\n✓ Script selesai dijalankan.\n";

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }

} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
