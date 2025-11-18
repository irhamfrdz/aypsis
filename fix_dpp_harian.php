<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== FIXING DPP FOR HARIAN TARIF ===\n\n";

// Ambil semua data dengan tarif Harian
$tagihanList = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('tarif', 'Harian')
    ->get();

echo "Total data dengan tarif Harian: " . $tagihanList->count() . "\n\n";

// Get pricelist untuk perhitungan
$pricelists = DB::table('master_pricelist_sewa_kontainers')
    ->where('tarif', 'Harian')
    ->get()
    ->keyBy(function($item) {
        return $item->vendor . '_' . $item->ukuran_kontainer;
    });

$fixedCount = 0;
$skippedCount = 0;
$errorCount = 0;

echo "Memperbaiki perhitungan DPP...\n\n";

foreach ($tagihanList as $tagihan) {
    $start = Carbon::parse($tagihan->tanggal_awal);
    $end = Carbon::parse($tagihan->tanggal_akhir);
    $jumlahHari = $start->diffInDays($end) + 1;
    
    $key = $tagihan->vendor . '_' . $tagihan->size;
    $pricelist = $pricelists->get($key);
    
    if (!$pricelist) {
        echo "⚠️  ID {$tagihan->id}: Pricelist tidak ditemukan untuk {$tagihan->vendor} size {$tagihan->size} - SKIP\n";
        $errorCount++;
        continue;
    }
    
    $dppSeharusnya = $pricelist->harga * $jumlahHari;
    $dppDatabase = $tagihan->dpp;
    $selisih = abs($dppSeharusnya - $dppDatabase);
    
    if ($selisih > 1) { // Toleransi pembulatan 1
        // Calculate new financial values
        $dppBaru = $dppSeharusnya;
        $ppnBaru = $dppBaru * 0.11;
        $pphBaru = $dppBaru * 0.02;
        $grandTotalBaru = $dppBaru + $ppnBaru - $pphBaru;
        
        echo "✓ ID {$tagihan->id}: {$tagihan->nomor_kontainer}\n";
        echo "  Masa: {$tagihan->masa} ({$jumlahHari} hari)\n";
        echo "  DPP: Rp " . number_format($dppDatabase, 0, ',', '.') . " → Rp " . number_format($dppBaru, 0, ',', '.') . "\n";
        echo "  PPN: Rp " . number_format($tagihan->ppn, 0, ',', '.') . " → Rp " . number_format($ppnBaru, 0, ',', '.') . "\n";
        echo "  PPH: Rp " . number_format($tagihan->pph, 0, ',', '.') . " → Rp " . number_format($pphBaru, 0, ',', '.') . "\n";
        echo "  Grand Total: Rp " . number_format($tagihan->grand_total, 0, ',', '.') . " → Rp " . number_format($grandTotalBaru, 0, ',', '.') . "\n";
        
        // Update database
        DB::table('daftar_tagihan_kontainer_sewa')
            ->where('id', $tagihan->id)
            ->update([
                'dpp' => $dppBaru,
                'ppn' => $ppnBaru,
                'pph' => $pphBaru,
                'grand_total' => $grandTotalBaru,
                'updated_at' => now()
            ]);
        
        $fixedCount++;
        echo "  Status: UPDATED\n\n";
    } else {
        $skippedCount++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total data: " . $tagihanList->count() . "\n";
echo "Data DIPERBAIKI: {$fixedCount}\n";
echo "Data SUDAH BENAR: {$skippedCount}\n";
echo "Data ERROR: {$errorCount}\n\n";

if ($fixedCount > 0) {
    echo "✓ {$fixedCount} data berhasil diperbaiki!\n";
}

if ($errorCount > 0) {
    echo "⚠️  {$errorCount} data memiliki masalah (pricelist tidak ditemukan)\n";
}
