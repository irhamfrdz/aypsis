<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║        FIX KONTAINER RXTU4540180 PERIODE 7 → BULANAN            ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

// Find the specific tagihan
$tagihan = DB::table('daftar_tagihan_kontainer_sewa')
    ->where('nomor_kontainer', 'RXTU4540180')
    ->where('periode', 7)
    ->first();

if (!$tagihan) {
    echo "✗ Tagihan RXTU4540180 periode 7 tidak ditemukan!\n";
    exit;
}

echo "📋 DATA SAAT INI:\n";
echo str_repeat("─", 70) . "\n";
echo "ID             : {$tagihan->id}\n";
echo "Kontainer      : {$tagihan->nomor_kontainer}\n";
echo "Vendor         : {$tagihan->vendor}\n";
echo "Size           : {$tagihan->size}'\n";
echo "Periode        : {$tagihan->periode}\n";
echo "Masa           : {$tagihan->masa}\n";
echo "Tanggal Awal   : {$tagihan->tanggal_awal}\n";
echo "Tanggal Akhir  : {$tagihan->tanggal_akhir}\n";

$start = Carbon::parse($tagihan->tanggal_awal);
$end = Carbon::parse($tagihan->tanggal_akhir);
$jumlahHari = $start->diffInDays($end) + 1;

echo "Jumlah Hari    : {$jumlahHari} hari\n";
echo "Tarif          : {$tagihan->tarif}\n";
echo "DPP            : Rp " . number_format($tagihan->dpp ?? 0, 2, ',', '.') . "\n";
echo "PPN            : Rp " . number_format($tagihan->ppn ?? 0, 2, ',', '.') . "\n";
echo "PPH            : Rp " . number_format($tagihan->pph ?? 0, 2, ',', '.') . "\n";
echo "Grand Total    : Rp " . number_format($tagihan->grand_total ?? 0, 2, ',', '.') . "\n";
echo str_repeat("─", 70) . "\n\n";

// Get pricelist for Bulanan
$vendor = strtoupper(trim($tagihan->vendor));
$size = trim($tagihan->size);

$pricelist = DB::table('master_pricelist_sewa_kontainers')
    ->where('vendor', $vendor)
    ->where('ukuran_kontainer', $size)
    ->where('tarif', 'Bulanan')
    ->first();

if (!$pricelist) {
    echo "✗ Pricelist tidak ditemukan untuk {$vendor} size {$size} tarif Bulanan!\n";
    echo "\nTambahkan pricelist terlebih dahulu:\n";
    echo "INSERT INTO master_pricelist_sewa_kontainers \n";
    echo "(vendor, tarif, ukuran_kontainer, harga, tanggal_harga_awal, created_at, updated_at)\n";
    echo "VALUES \n";
    echo "('{$vendor}', 'Bulanan', '{$size}', 0.00, '2025-01-01', NOW(), NOW());\n";
    exit;
}

echo "📋 PRICELIST BULANAN:\n";
echo str_repeat("─", 70) . "\n";
echo "Vendor         : {$pricelist->vendor}\n";
echo "Size           : {$pricelist->ukuran_kontainer}'\n";
echo "Tarif          : {$pricelist->tarif}\n";
echo "Harga          : Rp " . number_format($pricelist->harga, 2, ',', '.') . "\n";
echo str_repeat("─", 70) . "\n\n";

// Calculate new values
$dppBaru = round($pricelist->harga, 2);
$dppNilaiLain = round($dppBaru * 11 / 12, 2);
$ppnBaru = round($dppNilaiLain * 0.12, 2);
$pphBaru = round($dppBaru * 0.02, 2);
$grandTotalBaru = round($dppBaru + $ppnBaru - $pphBaru, 2);

echo "📊 PERUBAHAN YANG AKAN DILAKUKAN:\n";
echo str_repeat("─", 70) . "\n";
printf("%-20s %20s → %20s\n", "Field", "Nilai Lama", "Nilai Baru");
echo str_repeat("─", 70) . "\n";
printf("%-20s %20s → %20s\n", "Tarif", $tagihan->tarif, "Bulanan");
printf("%-20s %20s → %20s\n", 
    "DPP", 
    'Rp ' . number_format($tagihan->dpp ?? 0, 0, ',', '.'),
    'Rp ' . number_format($dppBaru, 0, ',', '.')
);
printf("%-20s %20s → %20s\n", 
    "DPP Nilai Lain", 
    'Rp ' . number_format($tagihan->dpp_nilai_lain ?? 0, 0, ',', '.'),
    'Rp ' . number_format($dppNilaiLain, 0, ',', '.')
);
printf("%-20s %20s → %20s\n", 
    "PPN", 
    'Rp ' . number_format($tagihan->ppn ?? 0, 0, ',', '.'),
    'Rp ' . number_format($ppnBaru, 0, ',', '.')
);
printf("%-20s %20s → %20s\n", 
    "PPH", 
    'Rp ' . number_format($tagihan->pph ?? 0, 0, ',', '.'),
    'Rp ' . number_format($pphBaru, 0, ',', '.')
);
printf("%-20s %20s → %20s\n", 
    "Grand Total", 
    'Rp ' . number_format($tagihan->grand_total ?? 0, 0, ',', '.'),
    'Rp ' . number_format($grandTotalBaru, 0, ',', '.')
);
echo str_repeat("─", 70) . "\n\n";

// Confirm
echo "⚠️  Lanjutkan perbaikan? (y/n): ";
$handle = fopen("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'y' && trim($line) != 'Y'){
    echo "Dibatalkan.\n";
    exit;
}
fclose($handle);

echo "\n";

// Update database
try {
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
    
    echo "✓ Berhasil! RXTU4540180 periode 7 telah diubah ke tarif Bulanan.\n\n";
    
    // Verify
    $updated = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('id', $tagihan->id)
        ->first();
    
    echo "📋 DATA SETELAH PERBAIKAN:\n";
    echo str_repeat("─", 70) . "\n";
    echo "Tarif          : {$updated->tarif}\n";
    echo "DPP            : Rp " . number_format($updated->dpp ?? 0, 2, ',', '.') . "\n";
    echo "PPN            : Rp " . number_format($updated->ppn ?? 0, 2, ',', '.') . "\n";
    echo "PPH            : Rp " . number_format($updated->pph ?? 0, 2, ',', '.') . "\n";
    echo "Grand Total    : Rp " . number_format($updated->grand_total ?? 0, 2, ',', '.') . "\n";
    echo str_repeat("─", 70) . "\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";
