<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

echo "=== PENCARIAN DPP TARIF HARIAN YANG SALAH ===\n\n";

// 1. Ambil semua tagihan dengan tarif harian
$tagihanHarian = DaftarTagihanKontainerSewa::where('tarif', 'Harian')
    ->orderBy('vendor')
    ->orderBy('size')
    ->orderBy('nomor_kontainer')
    ->orderBy('periode')
    ->get();

echo "Total tagihan dengan tarif harian: " . $tagihanHarian->count() . "\n\n";

if ($tagihanHarian->isEmpty()) {
    echo "Tidak ada tagihan dengan tarif harian ditemukan.\n";
    exit;
}

// 2. Group by vendor dan size untuk efisiensi
$masterPricelists = [];
$wrongDppData = [];
$correctDppData = [];

foreach ($tagihanHarian as $tagihan) {
    $key = $tagihan->vendor . '_' . $tagihan->size;
    
    // Cache master pricelist untuk menghindari query berulang
    if (!isset($masterPricelists[$key])) {
        $masterPricelists[$key] = MasterPricelistSewaKontainer::where('vendor', $tagihan->vendor)
            ->where('ukuran_kontainer', $tagihan->size)
            ->where('tarif', 'harian')
            ->first();
    }
    
    $masterPricelist = $masterPricelists[$key];
    
    if (!$masterPricelist) {
        // Jika tidak ada master pricelist harian, skip
        continue;
    }
    
    // Hitung jumlah hari
    $tanggalAwal = new DateTime($tagihan->tanggal_awal);
    $tanggalAkhir = new DateTime($tagihan->tanggal_akhir);
    $jumlahHari = $tanggalAwal->diff($tanggalAkhir)->days + 1;
    
    // Hitung DPP yang seharusnya
    $expectedDpp = $masterPricelist->harga * $jumlahHari;
    $actualDpp = (float)$tagihan->dpp;
    
    // Toleransi untuk pembulatan (1 rupiah)
    $tolerance = 1;
    
    if (abs($expectedDpp - $actualDpp) > $tolerance) {
        // DPP salah
        $wrongDppData[] = [
            'kontainer' => $tagihan->nomor_kontainer,
            'vendor' => $tagihan->vendor,
            'size' => $tagihan->size,
            'periode' => $tagihan->periode,
            'masa' => $tagihan->masa,
            'hari' => $jumlahHari,
            'tarif_per_hari' => $masterPricelist->harga,
            'expected_dpp' => $expectedDpp,
            'actual_dpp' => $actualDpp,
            'selisih' => $actualDpp - $expectedDpp,
            'id' => $tagihan->id,
            'tanggal_awal' => $tagihan->tanggal_awal,
            'tanggal_akhir' => $tagihan->tanggal_akhir,
        ];
    } else {
        // DPP benar
        $correctDppData[] = [
            'kontainer' => $tagihan->nomor_kontainer,
            'vendor' => $tagihan->vendor,
            'size' => $tagihan->size,
            'periode' => $tagihan->periode,
            'expected_dpp' => $expectedDpp,
            'actual_dpp' => $actualDpp,
        ];
    }
}

// 3. Tampilkan hasil
echo "=== HASIL ANALISIS ===\n";
echo "DPP Benar: " . count($correctDppData) . "\n";
echo "DPP Salah: " . count($wrongDppData) . "\n\n";

if (!empty($wrongDppData)) {
    echo "=== DPP TARIF HARIAN YANG SALAH ===\n";
    echo str_pad('No', 3) . " | " . 
         str_pad('Kontainer', 15) . " | " .
         str_pad('V', 4) . " | " .
         str_pad('S', 2) . " | " .
         str_pad('P', 2) . " | " .
         str_pad('Hari', 4) . " | " .
         str_pad('Expected DPP', 12) . " | " .
         str_pad('Actual DPP', 12) . " | " .
         str_pad('Selisih', 12) . "\n";
    echo str_repeat('-', 85) . "\n";
    
    foreach ($wrongDppData as $index => $data) {
        $no = $index + 1;
        echo str_pad($no, 3) . " | " .
             str_pad($data['kontainer'], 15) . " | " .
             str_pad($data['vendor'], 4) . " | " .
             str_pad($data['size'], 2) . " | " .
             str_pad($data['periode'], 2) . " | " .
             str_pad($data['hari'], 4) . " | " .
             str_pad(number_format($data['expected_dpp'], 0), 12) . " | " .
             str_pad(number_format($data['actual_dpp'], 0), 12) . " | " .
             str_pad(number_format($data['selisih'], 0), 12) . "\n";
    }
    
    echo "\n=== DETAIL DPP YANG SALAH ===\n";
    foreach ($wrongDppData as $index => $data) {
        $no = $index + 1;
        echo "{$no}. {$data['kontainer']} - Periode {$data['periode']}\n";
        echo "   Vendor: {$data['vendor']}, Size: {$data['size']}ft\n";
        echo "   Masa: {$data['masa']}\n";
        echo "   Hari: {$data['hari']}, Tarif/hari: Rp " . number_format($data['tarif_per_hari'], 0, ',', '.') . "\n";
        echo "   Expected: Rp " . number_format($data['expected_dpp'], 0, ',', '.') . 
             " ({$data['tarif_per_hari']} × {$data['hari']})\n";
        echo "   Actual: Rp " . number_format($data['actual_dpp'], 0, ',', '.') . "\n";
        echo "   Selisih: Rp " . number_format($data['selisih'], 0, ',', '.') . "\n";
        echo "   ID: {$data['id']}\n\n";
    }
    
    // Group by pattern untuk analisis
    echo "=== ANALISIS POLA KESALAHAN ===\n";
    $patterns = [];
    foreach ($wrongDppData as $data) {
        $pattern = $data['vendor'] . '_' . $data['size'] . 'ft';
        if (!isset($patterns[$pattern])) {
            $patterns[$pattern] = ['count' => 0, 'containers' => []];
        }
        $patterns[$pattern]['count']++;
        $patterns[$pattern]['containers'][] = $data['kontainer'] . '_P' . $data['periode'];
    }
    
    foreach ($patterns as $pattern => $info) {
        echo "- {$pattern}: {$info['count']} kasus\n";
        echo "  Kontainer: " . implode(', ', array_slice($info['containers'], 0, 5));
        if (count($info['containers']) > 5) {
            echo " (+" . (count($info['containers']) - 5) . " lainnya)";
        }
        echo "\n";
    }
    
} else {
    echo "✅ Semua DPP tarif harian sudah benar!\n";
}

// 4. Sample DPP yang benar
if (!empty($correctDppData) && empty($wrongDppData)) {
    echo "\n=== SAMPLE DPP YANG BENAR ===\n";
    $samples = array_slice($correctDppData, 0, 5);
    foreach ($samples as $data) {
        echo "- {$data['kontainer']} P{$data['periode']}: Rp " . 
             number_format($data['actual_dpp'], 0, ',', '.') . " ✅\n";
    }
}

echo "\n=== ANALISIS SELESAI ===\n";