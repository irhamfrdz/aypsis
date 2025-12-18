<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║   FIX DPP BERDASARKAN MASTER PRICELIST SEWA KONTAINER           ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

// Get all pricelist data
$pricelists = DB::table('master_pricelist_sewa_kontainers')
    ->orderBy('vendor')
    ->orderBy('ukuran_kontainer')
    ->orderBy('tarif')
    ->get()
    ->keyBy(function($item) {
        return strtoupper($item->vendor) . '_' . $item->ukuran_kontainer . '_' . strtoupper($item->tarif);
    });

echo "📋 Master Pricelist yang tersedia:\n";
echo str_repeat("─", 70) . "\n";
printf("%-10s %-10s %-10s %15s\n", "VENDOR", "SIZE", "TARIF", "HARGA");
echo str_repeat("─", 70) . "\n";
foreach ($pricelists as $pricelist) {
    printf("%-10s %-10s %-10s %15s\n", 
        $pricelist->vendor, 
        $pricelist->ukuran_kontainer, 
        $pricelist->tarif,
        'Rp ' . number_format($pricelist->harga, 0, ',', '.')
    );
}
echo str_repeat("─", 70) . "\n\n";

// Confirm before proceeding
echo "⚠️  Script ini akan memperbaiki DPP di tabel daftar_tagihan_kontainer_sewa\n";
echo "   berdasarkan harga di master_pricelist_sewa_kontainers.\n\n";
echo "Lanjutkan? (y/n): ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'y' && trim($line) != 'Y'){
    echo "Dibatalkan.\n";
    exit;
}
fclose($handle);

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                    MEMULAI PERBAIKAN DPP                         ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

// Get all tagihan
$tagihans = DB::table('daftar_tagihan_kontainer_sewa')
    ->whereNotLike('nomor_kontainer', 'GROUP_SUMMARY_%')
    ->whereNotLike('nomor_kontainer', 'GROUP_TEMPLATE%')
    ->orderBy('vendor')
    ->orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->get();

echo "Total tagihan yang akan diproses: " . $tagihans->count() . "\n\n";

$stats = [
    'bulanan' => [
        'fixed' => 0,
        'already_correct' => 0,
        'no_pricelist' => 0,
        'error' => 0
    ],
    'harian' => [
        'fixed' => 0,
        'already_correct' => 0,
        'no_pricelist' => 0,
        'error' => 0
    ]
];

$errorDetails = [];

foreach ($tagihans as $tagihan) {
    try {
        $vendor = strtoupper(trim($tagihan->vendor ?? ''));
        $size = trim($tagihan->size ?? '');
        $tarif = strtoupper(trim($tagihan->tarif ?? ''));
        
        // Skip if vendor is empty
        if (empty($vendor)) {
            $tarifType = strtolower($tarif ?: 'harian');
            $stats[$tarifType]['no_pricelist']++;
            $errorDetails[] = "ID {$tagihan->id}: Vendor kosong";
            continue;
        }
        
        // Build pricelist key
        $pricelistKey = $vendor . '_' . $size . '_' . $tarif;
        $pricelist = $pricelists->get($pricelistKey);
        
        if (!$pricelist) {
            $tarifType = strtolower($tarif);
            $stats[$tarifType]['no_pricelist']++;
            $errorDetails[] = "ID {$tagihan->id}: Pricelist tidak ditemukan untuk {$vendor} size {$size} tarif {$tarif}";
            continue;
        }
        
        // Calculate correct DPP based on tarif
        $dppSeharusnya = 0;
        
        if ($tarif === 'BULANAN') {
            // Untuk bulanan: gunakan harga flat dari pricelist (tidak dikalikan periode)
            $dppSeharusnya = $pricelist->harga;
            
        } elseif ($tarif === 'HARIAN') {
            // Untuk harian: harga per hari × jumlah hari
            if (!$tagihan->tanggal_awal || !$tagihan->tanggal_akhir) {
                $stats['harian']['error']++;
                $errorDetails[] = "ID {$tagihan->id}: Tanggal awal/akhir tidak ada untuk tarif harian";
                continue;
            }
            
            $start = Carbon::parse($tagihan->tanggal_awal);
            $end = Carbon::parse($tagihan->tanggal_akhir);
            $jumlahHari = $start->diffInDays($end) + 1;
            $dppSeharusnya = $pricelist->harga * $jumlahHari;
        } else {
            $stats['harian']['error']++;
            $errorDetails[] = "ID {$tagihan->id}: Tarif tidak dikenal: {$tarif}";
            continue;
        }
        
        // Round to 2 decimal places
        $dppSeharusnya = round($dppSeharusnya, 2);
        $dppDatabase = round(floatval($tagihan->dpp ?? 0), 2);
        $selisih = abs($dppSeharusnya - $dppDatabase);
        
        $tarifType = strtolower($tarif);
        
        // Toleransi pembulatan: 1 rupiah
        if ($selisih > 1) {
            // DPP berbeda, perlu diperbaiki
            
            // Calculate new taxes based on model logic
            $dppNilaiLain = round($dppSeharusnya * 11 / 12, 2);
            $ppnBaru = round($dppNilaiLain * 0.12, 2);
            $pphBaru = round($dppSeharusnya * 0.02, 2);
            $grandTotalBaru = round($dppSeharusnya + $ppnBaru - $pphBaru, 2);
            
            // Display change
            echo "✓ ID {$tagihan->id}: {$tagihan->nomor_kontainer} - Periode {$tagihan->periode}\n";
            echo "  Vendor: {$vendor}, Size: {$size}, Tarif: {$tarif}\n";
            if ($tarif === 'HARIAN') {
                echo "  Masa: {$tagihan->masa} ({$jumlahHari} hari)\n";
            }
            echo "  DPP        : Rp " . number_format($dppDatabase, 2, ',', '.') . 
                 " → Rp " . number_format($dppSeharusnya, 2, ',', '.') . "\n";
            echo "  PPN        : Rp " . number_format($tagihan->ppn ?? 0, 2, ',', '.') . 
                 " → Rp " . number_format($ppnBaru, 2, ',', '.') . "\n";
            echo "  PPH        : Rp " . number_format($tagihan->pph ?? 0, 2, ',', '.') . 
                 " → Rp " . number_format($pphBaru, 2, ',', '.') . "\n";
            echo "  Grand Total: Rp " . number_format($tagihan->grand_total ?? 0, 2, ',', '.') . 
                 " → Rp " . number_format($grandTotalBaru, 2, ',', '.') . "\n";
            
            // Update database
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
            
            $stats[$tarifType]['fixed']++;
            echo "  Status: ✓ UPDATED\n\n";
            
        } else {
            // DPP sudah benar
            $stats[$tarifType]['already_correct']++;
        }
        
    } catch (\Exception $e) {
        $tarifType = strtolower($tagihan->tarif ?? 'harian');
        $stats[$tarifType]['error']++;
        $errorDetails[] = "ID {$tagihan->id}: Error - " . $e->getMessage();
    }
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                          SUMMARY                                 ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 TARIF BULANAN:\n";
echo "  ✓ Diperbaiki        : " . $stats['bulanan']['fixed'] . " data\n";
echo "  ✓ Sudah benar       : " . $stats['bulanan']['already_correct'] . " data\n";
echo "  ⚠ Pricelist missing : " . $stats['bulanan']['no_pricelist'] . " data\n";
echo "  ✗ Error             : " . $stats['bulanan']['error'] . " data\n";
echo "\n";

echo "📊 TARIF HARIAN:\n";
echo "  ✓ Diperbaiki        : " . $stats['harian']['fixed'] . " data\n";
echo "  ✓ Sudah benar       : " . $stats['harian']['already_correct'] . " data\n";
echo "  ⚠ Pricelist missing : " . $stats['harian']['no_pricelist'] . " data\n";
echo "  ✗ Error             : " . $stats['harian']['error'] . " data\n";
echo "\n";

$totalFixed = $stats['bulanan']['fixed'] + $stats['harian']['fixed'];
$totalCorrect = $stats['bulanan']['already_correct'] + $stats['harian']['already_correct'];
$totalNoPricelist = $stats['bulanan']['no_pricelist'] + $stats['harian']['no_pricelist'];
$totalError = $stats['bulanan']['error'] + $stats['harian']['error'];

echo "📊 TOTAL:\n";
echo "  ✓ Total diperbaiki  : " . $totalFixed . " data\n";
echo "  ✓ Total sudah benar : " . $totalCorrect . " data\n";
echo "  ⚠ Total no pricelist: " . $totalNoPricelist . " data\n";
echo "  ✗ Total error       : " . $totalError . " data\n";
echo "\n";

// Show errors if any
if (!empty($errorDetails)) {
    echo "╔══════════════════════════════════════════════════════════════════╗\n";
    echo "║                       ERROR DETAILS                              ║\n";
    echo "╚══════════════════════════════════════════════════════════════════╝\n\n";
    foreach ($errorDetails as $error) {
        echo "⚠️  {$error}\n";
    }
    echo "\n";
}

if ($totalFixed > 0) {
    echo "✓ Script selesai! {$totalFixed} data berhasil diperbaiki.\n";
} else {
    echo "ℹ️  Tidak ada data yang perlu diperbaiki.\n";
}

if ($totalNoPricelist > 0) {
    echo "\n⚠️  PERHATIAN: {$totalNoPricelist} data tidak memiliki pricelist di master.\n";
    echo "   Silakan tambahkan pricelist untuk vendor dan ukuran kontainer tersebut.\n";
}

echo "\n";
