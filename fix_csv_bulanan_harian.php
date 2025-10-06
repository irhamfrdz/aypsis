<?php
/*
Script untuk memperbaiki CSV dengan membedakan tarif BULANAN vs HARIAN
- Tarif BULANAN: DPP = tarif per bulan (tidak dikalikan hari)
- Tarif HARIAN: DPP = tarif per hari × jumlah hari
*/

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MasterPricelistSewaKontainer;

// Fungsi untuk menghitung selisih hari
function hitungSelisihHari($tanggalAwal, $tanggalAkhir) {
    $start = DateTime::createFromFormat('d/m/Y', $tanggalAwal);
    $end = DateTime::createFromFormat('d/m/Y', $tanggalAkhir);

    if (!$start || !$end) {
        return 0;
    }

    $interval = $start->diff($end);
    return $interval->days + 1; // +1 karena termasuk hari pertama
}

// Fungsi untuk menghitung DPP berdasarkan master pricelist
function hitungDppDariMasterPricelist($vendor, $ukuranKontainer, $jumlahHari, $tarifTextFromCsv) {
    // Cek master pricelist
    $masterPricelist = MasterPricelistSewaKontainer::where('vendor', $vendor)
        ->where('ukuran_kontainer', $ukuranKontainer)
        ->first();

    if ($masterPricelist) {
        $tarifMaster = strtolower($masterPricelist->tarif);
        $harga = $masterPricelist->harga;

        if ($tarifMaster === 'bulanan') {
            // Untuk tarif bulanan, DPP = harga master (tidak dikalikan hari)
            $dpp = $harga;
            $info = "Master Pricelist BULANAN: {$vendor} {$ukuranKontainer}ft = " . number_format($harga) . "/bulan → DPP = " . number_format($dpp);
        } else {
            // Untuk tarif harian, DPP = harga master × jumlah hari
            $dpp = $harga * $jumlahHari;
            $info = "Master Pricelist HARIAN: {$vendor} {$ukuranKontainer}ft = " . number_format($harga) . "/hari × {$jumlahHari} hari → DPP = " . number_format($dpp);
        }

        echo "✓ $info\n";
        return $dpp;
    }

    // Fallback ke tarif default harian
    if ($vendor === 'DPE') {
        $tarif = ($ukuranKontainer == '20') ? 25000 : 35000;
    } else if ($vendor === 'ZONA') {
        $tarif = ($ukuranKontainer == '20') ? 20000 : 30000;
    } else {
        $tarif = 25000;
    }

    $dpp = $tarif * $jumlahHari;
    echo "! Fallback Tarif Harian: {$vendor} {$ukuranKontainer}ft = " . number_format($tarif) . "/hari × {$jumlahHari} hari → DPP = " . number_format($dpp) . " (Master pricelist tidak ditemukan)\n";
    return $dpp;
}

// File input dan output
$inputFile = 'export_tagihan_kontainer_sewa.csv';
$outputFile = 'export_tagihan_kontainer_sewa_BULANAN_HARIAN_FIXED.csv';

if (!file_exists($inputFile)) {
    die("File input tidak ditemukan: $inputFile\n");
}

echo "=== PERBAIKAN CSV DENGAN TARIF BULANAN/HARIAN YANG BENAR ===\n";
echo "Input: $inputFile\n";
echo "Output: $outputFile\n\n";

// Tampilkan Master Pricelist
echo "=== MASTER PRICELIST YANG TERSEDIA ===\n";
$masterPricelists = MasterPricelistSewaKontainer::all();
foreach ($masterPricelists as $pricelist) {
    $type = strtoupper($pricelist->tarif);
    echo "- {$pricelist->vendor} {$pricelist->ukuran_kontainer}ft: " . number_format($pricelist->harga) . " ($type)\n";
}
echo "\n=== MULAI PERBAIKAN DATA ===\n";

// Baca file CSV
$handle = fopen($inputFile, 'r');
if (!$handle) {
    die("Tidak bisa membuka file: $inputFile\n");
}

// Siapkan file output
$outputHandle = fopen($outputFile, 'w');
if (!$outputHandle) {
    die("Tidak bisa membuat file output: $outputFile\n");
}

$rowNumber = 0;
$perbaikanCount = 0;

while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
    $rowNumber++;

    // Header row - tulis langsung
    if ($rowNumber == 1) {
        fputcsv($outputHandle, $data, ';');
        continue;
    }

    // Parse data
    $group = $data[0];
    $vendor = $data[1];
    $nomorKontainer = $data[2];
    $size = $data[3];
    $tanggalAwal = $data[4];
    $tanggalAkhir = $data[5];
    $periode = $data[6];
    $masa = $data[7];
    $tarifText = $data[8]; // Bulanan/Harian dari CSV
    $status = $data[9];
    $oldDpp = (float)$data[10];
    $adjustment = (float)$data[11];
    $oldDppNilaiLain = (float)$data[12];
    $oldPpn = (float)$data[13];
    $oldPph = (float)$data[14];
    $oldGrandTotal = (float)$data[15];
    $statusPranota = $data[16];
    $pranotaId = $data[17];

    // Hitung jumlah hari
    $jumlahHari = hitungSelisihHari($tanggalAwal, $tanggalAkhir);

    if ($jumlahHari <= 0) {
        echo "⚠ Skipping row $rowNumber: Invalid date range\n";
        fputcsv($outputHandle, $data, ';');
        continue;
    }

    // Hitung DPP baru berdasarkan master pricelist (bulanan/harian)
    $newDpp = hitungDppDariMasterPricelist($vendor, $size, $jumlahHari, $tarifText);

    // Hitung komponen finansial lainnya berdasarkan DPP baru + adjustment
    $adjustedDpp = $newDpp + $adjustment;
    $newPpn = $adjustedDpp * 0.11; // 11%
    $newPph = $adjustedDpp * 0.02; // 2%
    $newDppNilaiLain = $adjustedDpp * (11/12); // 11/12 dari adjusted DPP
    $newGrandTotal = $adjustedDpp + $newPpn - $newPph;

    // Cek apakah ada perubahan
    if (abs($newDpp - $oldDpp) > 0.01) {
        $perbaikanCount++;
        echo sprintf(
            "Row %d: %s %s P%d (%d hari) %s - DPP: %s → %s (Δ: %+s)\n",
            $rowNumber,
            $nomorKontainer,
            $size . 'ft',
            $periode,
            $jumlahHari,
            $tarifText,
            number_format($oldDpp),
            number_format($newDpp),
            number_format($newDpp - $oldDpp)
        );
    }

    // Update data dengan nilai baru
    $data[10] = number_format($newDpp, 2, '.', ''); // DPP
    $data[12] = number_format($newDppNilaiLain, 2, '.', ''); // DPP Nilai Lain
    $data[13] = number_format($newPpn, 2, '.', ''); // PPN
    $data[14] = number_format($newPph, 2, '.', ''); // PPH
    $data[15] = number_format($newGrandTotal, 2, '.', ''); // Grand Total

    // Tulis ke file output
    fputcsv($outputHandle, $data, ';');
}

fclose($handle);
fclose($outputHandle);

echo "\n=== HASIL PERBAIKAN ===\n";
echo "Total baris diproses: " . ($rowNumber - 1) . "\n";
echo "Total perbaikan: $perbaikanCount\n";
echo "File output: $outputFile\n";

if ($perbaikanCount > 0) {
    echo "\n✓ Perbaikan selesai! File CSV sudah menggunakan tarif BULANAN/HARIAN yang benar.\n";
    echo "Silakan download file: $outputFile\n";
} else {
    echo "\n! Tidak ada perubahan yang diperlukan.\n";
}

echo "\n=== PENJELASAN HASIL ===\n";
echo "- DPE 40ft (Bulanan): DPP = 1,500,000 per periode (tidak dikalikan hari)\n";
echo "- DPE 20ft (Harian): DPP = 25,000 × jumlah hari\n";
echo "- ZONA 40ft (Bulanan): DPP = 1,261,261 per periode\n";
echo "- ZONA 20ft/40ft (Harian): DPP = tarif × jumlah hari\n";

?>
