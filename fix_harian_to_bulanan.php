<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║   FIX TARIF HARIAN → BULANAN (UNTUK PERIODE BULANAN PENUH)     ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

// Get pricelist for Bulanan tarif
$pricelists = DB::table('master_pricelist_sewa_kontainers')
    ->where('tarif', 'Bulanan')
    ->get()
    ->keyBy(function($item) {
        return strtoupper($item->vendor) . '_' . $item->ukuran_kontainer;
    });

echo "📋 Master Pricelist Bulanan tersedia: {$pricelists->count()}\n";
foreach ($pricelists as $pricelist) {
    echo "  - {$pricelist->vendor} {$pricelist->ukuran_kontainer}': Rp " . number_format($pricelist->harga, 0, ',', '.') . "\n";
}
echo "\n";

// Find all Harian tarif that are actually full month periods (28-31 days)
$tagihanHarian = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('tarif', 'Harian')
    ->whereNotLike('nomor_kontainer', 'GROUP_SUMMARY_%')
    ->whereNotLike('nomor_kontainer', 'GROUP_TEMPLATE%')
    ->whereNotNull('tanggal_awal')
    ->whereNotNull('tanggal_akhir')
    ->orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->get();

echo "Total tagihan dengan tarif Harian: {$tagihanHarian->count()}\n\n";

$candidates = [];

foreach ($tagihanHarian as $tagihan) {
    $start = Carbon::parse($tagihan->tanggal_awal);
    $end = Carbon::parse($tagihan->tanggal_akhir);
    $jumlahHari = $start->diffInDays($end) + 1;
    $daysInMonth = $start->daysInMonth;
    
    // Check if this is a full month period (allow 1 day tolerance for month end variations)
    // Full month is 28-31 days depending on the month
    if ($jumlahHari >= 28 && $jumlahHari <= 31) {
        // Check if it's approximately a full month
        // For example: if start is 4-Sep and end is 3-Oct, that's 30 days (full month)
        $isFullMonth = false;
        
        // Method 1: Check if it's exactly month-to-month (e.g., 4-Sep to 3-Oct)
        $expectedEnd = $start->copy()->addMonthsNoOverflow(1)->subDay();
        if ($end->equalTo($expectedEnd)) {
            $isFullMonth = true;
        }
        
        // Method 2: Check if days match the month length (±1 day tolerance)
        if (abs($jumlahHari - $daysInMonth) <= 1) {
            $isFullMonth = true;
        }
        
        if ($isFullMonth) {
            $candidates[] = [
                'tagihan' => $tagihan,
                'jumlah_hari' => $jumlahHari,
                'days_in_month' => $daysInMonth
            ];
        }
    }
}

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║              KANDIDAT UNTUK DIUBAH KE TARIF BULANAN              ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

echo "Ditemukan " . count($candidates) . " tagihan yang seharusnya tarif Bulanan\n\n";

if (empty($candidates)) {
    echo "✓ Tidak ada data yang perlu diperbaiki.\n";
    exit;
}

// Show candidates
echo str_repeat("─", 120) . "\n";
printf("%-5s %-15s %-10s %-8s %-10s %-12s %15s %15s\n", 
    "ID", "KONTAINER", "VENDOR", "SIZE", "PERIODE", "MASA", "DPP LAMA", "DPP BARU");
echo str_repeat("─", 120) . "\n";

foreach ($candidates as $candidate) {
    $tagihan = $candidate['tagihan'];
    $vendor = strtoupper(trim($tagihan->vendor ?? ''));
    $size = trim($tagihan->size ?? '');
    
    if (empty($vendor)) {
        continue; // Skip if vendor is empty
    }
    
    $pricelistKey = $vendor . '_' . $size;
    $pricelist = $pricelists->get($pricelistKey);
    
    if (!$pricelist) {
        $dppBaru = "NO PRICELIST";
    } else {
        $dppBaru = 'Rp ' . number_format($pricelist->harga, 0, ',', '.');
    }
    
    printf("%-5s %-15s %-10s %-8s %-10s %-12s %15s %15s\n",
        $tagihan->id,
        $tagihan->nomor_kontainer,
        $vendor,
        $size,
        $tagihan->periode,
        $candidate['jumlah_hari'] . ' hari',
        'Rp ' . number_format($tagihan->dpp ?? 0, 0, ',', '.'),
        $dppBaru
    );
}
echo str_repeat("─", 120) . "\n\n";

// Confirm
echo "⚠️  Script ini akan mengubah tarif Harian → Bulanan dan recalculate DPP.\n";
echo "Lanjutkan? (y/n): ";
$handle = fopen("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'y' && trim($line) != 'Y'){
    echo "Dibatalkan.\n";
    exit;
}
fclose($handle);

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                    MEMULAI PERBAIKAN                             ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$stats = [
    'fixed' => 0,
    'no_pricelist' => 0,
    'error' => 0
];

foreach ($candidates as $candidate) {
    $tagihan = $candidate['tagihan'];
    
    try {
        $vendor = strtoupper(trim($tagihan->vendor ?? ''));
        $size = trim($tagihan->size ?? '');
        
        if (empty($vendor)) {
            $stats['no_pricelist']++;
            echo "⚠️  ID {$tagihan->id}: Vendor kosong - SKIP\n";
            continue;
        }
        
        $pricelistKey = $vendor . '_' . $size;
        $pricelist = $pricelists->get($pricelistKey);
        
        if (!$pricelist) {
            $stats['no_pricelist']++;
            echo "⚠️  ID {$tagihan->id}: Pricelist tidak ditemukan untuk {$vendor} size {$size} - SKIP\n";
            continue;
        }
        
        // Calculate new values with Bulanan tarif
        $dppBaru = round($pricelist->harga, 2);
        $dppNilaiLain = round($dppBaru * 11 / 12, 2);
        $ppnBaru = round($dppNilaiLain * 0.12, 2);
        $pphBaru = round($dppBaru * 0.02, 2);
        $grandTotalBaru = round($dppBaru + $ppnBaru - $pphBaru, 2);
        
        echo "✓ ID {$tagihan->id}: {$tagihan->nomor_kontainer} - Periode {$tagihan->periode}\n";
        echo "  Tarif      : Harian → Bulanan\n";
        echo "  DPP        : Rp " . number_format($tagihan->dpp ?? 0, 2, ',', '.') . 
             " → Rp " . number_format($dppBaru, 2, ',', '.') . "\n";
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
                'tarif' => 'Bulanan',
                'dpp' => $dppBaru,
                'dpp_nilai_lain' => $dppNilaiLain,
                'ppn' => $ppnBaru,
                'pph' => $pphBaru,
                'grand_total' => $grandTotalBaru,
                'updated_at' => now()
            ]);
        
        $stats['fixed']++;
        echo "  Status: ✓ UPDATED\n\n";
        
    } catch (\Exception $e) {
        $stats['error']++;
        echo "✗ ID {$tagihan->id}: Error - " . $e->getMessage() . "\n\n";
    }
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                          SUMMARY                                 ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 HASIL:\n";
echo "  ✓ Berhasil diperbaiki  : {$stats['fixed']} data\n";
echo "  ⚠ No pricelist/vendor  : {$stats['no_pricelist']} data\n";
echo "  ✗ Error                : {$stats['error']} data\n";
echo "\n";

if ($stats['fixed'] > 0) {
    echo "✓ Script selesai! {$stats['fixed']} data berhasil diubah dari Harian → Bulanan.\n";
} else {
    echo "ℹ️  Tidak ada data yang berhasil diperbaiki.\n";
}

echo "\n";
