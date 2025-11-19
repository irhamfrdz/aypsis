<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Kontainer;
use App\Models\DaftarTagihanKontainerSewa;

echo "=== Checking Kontainer APZU3960241 ===\n";

// Check master kontainer
$kontainer = Kontainer::where('nomor_seri_gabungan', 'APZU3960241')->first();
if ($kontainer) {
    echo "Master Kontainer ditemukan:\n";
    echo "- Nomor: {$kontainer->nomor_seri_gabungan}\n";
    echo "- Vendor: {$kontainer->vendor}\n";
    echo "- Ukuran: {$kontainer->ukuran}\n";
    echo "- Tanggal Mulai Sewa: {$kontainer->tanggal_mulai_sewa}\n";
    echo "- Tanggal Selesai Sewa: " . ($kontainer->tanggal_selesai_sewa ?: 'NULL/Kosong') . "\n";
    echo "- Status: {$kontainer->status}\n";
} else {
    echo "Master Kontainer APZU3960241 tidak ditemukan!\n";
}

echo "\n=== Checking Tagihan Records ===\n";

// Check tagihan records
$tagihans = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'APZU3960241')
    ->orderBy('periode', 'asc')
    ->get();

if ($tagihans->count() > 0) {
    echo "Tagihan records ditemukan: {$tagihans->count()} periode\n";
    foreach ($tagihans as $tagihan) {
        echo "- Periode {$tagihan->periode}: {$tagihan->tanggal_awal} - " . ($tagihan->tanggal_akhir ?: 'NULL') . "\n";
        echo "  Vendor: {$tagihan->vendor}, Size: {$tagihan->size}\n";
        echo "  Masa: {$tagihan->masa}, Tarif: {$tagihan->tarif}\n";
    }
    
    $maxPeriode = $tagihans->max('periode');
    echo "\nMax periode saat ini: {$maxPeriode}\n";
    
    $lastRecord = $tagihans->where('periode', $maxPeriode)->first();
    if ($lastRecord && $lastRecord->tanggal_akhir) {
        echo "Tanggal akhir periode terakhir: {$lastRecord->tanggal_akhir}\n";
        
        $currentDate = \Carbon\Carbon::now();
        $lastEndDate = \Carbon\Carbon::parse($lastRecord->tanggal_akhir);
        echo "Tanggal sekarang: {$currentDate->format('Y-m-d')}\n";
        echo "Perlu next periode? " . ($currentDate->gt($lastEndDate) ? 'YA' : 'TIDAK') . "\n";
    } else {
        echo "WARNING: Periode terakhir tidak memiliki tanggal_akhir!\n";
    }
} else {
    echo "Tidak ada tagihan records untuk APZU3960241!\n";
}

echo "\n=== Kondisi untuk Create Next Periode ===\n";

echo "\n=== Logic untuk kontainer dengan tanggal selesai sewa ===\n";

$startDate = \Carbon\Carbon::parse($kontainer->tanggal_mulai_sewa);
$currentDate = \Carbon\Carbon::now();
$endDate = \Carbon\Carbon::parse($kontainer->tanggal_selesai_sewa);

echo "- Tanggal mulai sewa: {$startDate->format('Y-m-d')}\n";
echo "- Tanggal selesai sewa: {$endDate->format('Y-m-d')}\n";
echo "- Tanggal sekarang: {$currentDate->format('Y-m-d')}\n";

// Logic dari CreateNextPeriodeTagihan
if ($kontainer->tanggal_selesai_sewa) {
    // Container with end date - calculate periods until end date
    $totalMonths = intval($startDate->diffInMonths($endDate));
    $maxPeriode = $totalMonths + 1;
    echo "- Total months dari start ke end: {$totalMonths}\n";
    echo "- Max periode berdasarkan end date: {$maxPeriode}\n";
} else {
    // Ongoing container - calculate periods until now
    $totalMonths = intval($startDate->diffInMonths($currentDate));
    $maxPeriode = $totalMonths + 1;
    echo "- Total months dari start ke now: {$totalMonths}\n";
    echo "- Max periode berdasarkan current date: {$maxPeriode}\n";
}

echo "\n*** TAPI TUNGGU! ***\n";
echo "Kontainer sudah expired pada: {$endDate->format('Y-m-d')}\n";
echo "Tanggal sekarang: {$currentDate->format('Y-m-d')}\n";
echo "Status kontrak: " . ($currentDate->gt($endDate) ? 'EXPIRED' : 'AKTIF') . "\n";

if ($currentDate->gt($endDate)) {
    echo "\n=== Untuk kontainer EXPIRED ===\n";
    echo "Sistem SEHARUSNYA tetap membuat periode hingga tanggal sekarang!\n";
    echo "Karena tagihan bisa berlanjut meski kontrak habis.\n";
    
    // Calculate what SHOULD be the max periode (until current date)
    $totalMonthsToNow = intval($startDate->diffInMonths($currentDate));
    $maxPeriodeToNow = $totalMonthsToNow + 1;
    
    echo "- Max periode hingga sekarang: {$maxPeriodeToNow}\n";
    
    $existingMaxPeriode = DaftarTagihanKontainerSewa::where('vendor', $kontainer->vendor)
        ->where('nomor_kontainer', 'APZU3960241')
        ->where('tanggal_awal', $kontainer->tanggal_mulai_sewa)
        ->max('periode') ?? 0;
        
    echo "- Periode yang ada: {$existingMaxPeriode}\n";
    echo "- Periode yang kurang: " . ($maxPeriodeToNow - $existingMaxPeriode) . "\n";
    
    if ($existingMaxPeriode < $maxPeriodeToNow) {
        echo "*** HARUS BUAT PERIODE " . ($existingMaxPeriode + 1) . " sampai {$maxPeriodeToNow} ***\n";
    }
}

echo "\n=== Checking Periode 5 Detail ===\n";
$periode5 = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'APZU3960241')->where('periode', 5)->first();
if ($periode5) {
    echo "Periode 5 detail:\n";
    echo "- ID: {$periode5->id}\n";
    echo "- Tanggal awal: {$periode5->tanggal_awal}\n";
    echo "- Tanggal akhir: " . ($periode5->tanggal_akhir ?: 'NULL/KOSONG') . "\n";
    echo "- Masa: {$periode5->masa}\n";
    echo "- Tarif: {$periode5->tarif}\n";
    echo "- DPP: {$periode5->dpp}\n";
    
    echo "\nMengapa periode 5 tidak punya tanggal_akhir?\n";
    echo "- Kontainer end date: {$kontainer->tanggal_selesai_sewa}\n";
    echo "- Period start: {$periode5->tanggal_awal}\n";
    
    $periodStart = \Carbon\Carbon::parse($periode5->tanggal_awal);
    $periodEnd = $periodStart->copy()->addMonthsNoOverflow(1)->subDay();
    echo "- Calculated period end (normal): {$periodEnd->format('Y-m-d')}\n";
    
    $containerEnd = \Carbon\Carbon::parse($kontainer->tanggal_selesai_sewa);
    if ($periodEnd->gt($containerEnd)) {
        echo "- Period end capped by container end: {$containerEnd->format('Y-m-d')}\n";
        echo "*** PERIODE 5 SEHARUSNYA BERAKHIR PADA: {$containerEnd->format('Y-m-d')} ***\n";
    }
}