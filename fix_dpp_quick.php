<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== FIX DPP FROM MASTER PRICELIST (QUICK MODE) ===\n\n";

// Get all pricelist data
$pricelists = DB::table('master_pricelist_sewa_kontainers')
    ->get()
    ->keyBy(function($item) {
        return strtoupper($item->vendor) . '_' . $item->ukuran_kontainer . '_' . strtoupper($item->tarif);
    });

echo "Pricelist tersedia: {$pricelists->count()}\n\n";

// Get tagihan yang akan diperbaiki
$tagihans = DB::table('daftar_tagihan_kontainer_sewa')
    ->whereNotLike('nomor_kontainer', 'GROUP_SUMMARY_%')
    ->whereNotLike('nomor_kontainer', 'GROUP_TEMPLATE%')
    ->get();

echo "Total tagihan: {$tagihans->count()}\n\n";

$fixed = 0;
$skipped = 0;
$errors = 0;

foreach ($tagihans as $tagihan) {
    $vendor = strtoupper(trim($tagihan->vendor ?? ''));
    $size = trim($tagihan->size ?? '');
    $tarif = strtoupper(trim($tagihan->tarif ?? ''));
    
    // Skip if vendor is empty
    if (empty($vendor)) {
        $errors++;
        continue;
    }
    
    $pricelistKey = $vendor . '_' . $size . '_' . $tarif;
    $pricelist = $pricelists->get($pricelistKey);
    
    if (!$pricelist) {
        $errors++;
        continue;
    }
    
    // Calculate correct DPP
    $dppSeharusnya = 0;
    
    if ($tarif === 'BULANAN') {
        $dppSeharusnya = $pricelist->harga;
    } elseif ($tarif === 'HARIAN') {
        if (!$tagihan->tanggal_awal || !$tagihan->tanggal_akhir) {
            $errors++;
            continue;
        }
        $start = Carbon::parse($tagihan->tanggal_awal);
        $end = Carbon::parse($tagihan->tanggal_akhir);
        $jumlahHari = $start->diffInDays($end) + 1;
        $dppSeharusnya = $pricelist->harga * $jumlahHari;
    } else {
        $errors++;
        continue;
    }
    
    $dppSeharusnya = round($dppSeharusnya, 2);
    $dppDatabase = round(floatval($tagihan->dpp ?? 0), 2);
    
    if (abs($dppSeharusnya - $dppDatabase) > 1) {
        // Calculate taxes
        $dppNilaiLain = round($dppSeharusnya * 11 / 12, 2);
        $ppnBaru = round($dppNilaiLain * 0.12, 2);
        $pphBaru = round($dppSeharusnya * 0.02, 2);
        $grandTotalBaru = round($dppSeharusnya + $ppnBaru - $pphBaru, 2);
        
        // Update
        DB::table('daftar_tagihan_kontainer_sewa')
            ->where('id', $tagihan->id)
            ->update([
                'dpp' => $dppSeharusnya,
                'dpp_nilai_lain' => $dppNilaiLain,
                'ppn' => $ppnBaru,
                'pph' => $pphBaru,
                'grand_total' => $grandTotalBaru,
                'updated_at' => now()
            ]);
        
        $fixed++;
        echo "✓ ID {$tagihan->id}: {$tagihan->nomor_kontainer} - DPP updated\n";
    } else {
        $skipped++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Fixed   : {$fixed}\n";
echo "Skipped : {$skipped}\n";
echo "Errors  : {$errors}\n";
echo "\n";

if ($fixed > 0) {
    echo "✓ {$fixed} data berhasil diperbaiki!\n";
} else {
    echo "ℹ️  Tidak ada data yang perlu diperbaiki.\n";
}
