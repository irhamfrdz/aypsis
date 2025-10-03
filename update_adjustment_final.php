<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== Update Adjustment + Financial Data dari CSV ===\n\n";

$csvFile = 'C:\Users\amanda\Downloads\Tagihan Kontainer Sewa DPE.csv';
$handle = fopen($csvFile, 'r');

if (!$handle) {
    die("Error: Tidak dapat membuka file CSV\n");
}

// Baca dan skip header
$header = fgetcsv($handle, 0, ';');

$updated = 0;
$notFound = 0;
$errors = [];

$rowNum = 1;

while (($data = fgetcsv($handle, 0, ';')) !== false) {
    $rowNum++;

    // Skip baris kosong
    if (empty($data[1])) {
        continue;
    }

    $nomorKontainer = trim($data[1]);
    $tanggalAwal = trim($data[2]);
    $periode = trim($data[6]);
    $hari = trim($data[8]);
    $dpp = trim($data[9]);
    $adjustment = trim($data[12]);
    $ppn = trim($data[14]);
    $pph = trim($data[15]);
    $grandTotal = trim($data[16]);

    // Konversi format tanggal dari dd-mm-yyyy ke yyyy-mm-dd
    try {
        $tanggalAwalFormatted = Carbon::createFromFormat('d-m-Y', $tanggalAwal)->format('Y-m-d');
    } catch (\Exception $e) {
        $errors[] = "Baris $rowNum: Format tanggal tidak valid ($tanggalAwal)";
        continue;
    }

    // Cari record di database
    $record = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
        ->where('periode', $periode)
        ->where('tanggal_awal', $tanggalAwalFormatted)
        ->first();

    if (!$record) {
        $notFound++;
        echo "Baris $rowNum: Tidak ditemukan - $nomorKontainer (Periode $periode, Tanggal $tanggalAwalFormatted)\n";
        continue;
    }

    // Parse values dari CSV
    $hariValue = (int) $hari;
    $dppValue = (float) str_replace(['.', ','], ['', '.'], $dpp);

    $adjustmentValue = 0;
    if ($adjustment && $adjustment !== '-' && trim($adjustment) !== '') {
        $adjustmentValue = (float) str_replace(['.', ','], ['', '.'], $adjustment);
    }

    $ppnValue = 0;
    if ($ppn && trim($ppn) !== '') {
        $ppnValue = (float) str_replace(['.', ','], ['', '.'], $ppn);
    }

    $pphValue = 0;
    if ($pph && trim($pph) !== '') {
        $pphValue = (float) str_replace(['.', ','], ['', '.'], $pph);
    }

    $grandTotalValue = 0;
    if ($grandTotal && trim($grandTotal) !== '') {
        $grandTotalValue = (float) str_replace(['.', ','], ['', '.'], $grandTotal);
    }

    // Update record dengan nilai LANGSUNG dari CSV (tidak dihitung ulang)
    $record->dpp = $dppValue;
    $record->adjustment = $adjustmentValue;
    $record->ppn = round($ppnValue);
    $record->pph = round($pphValue);
    $record->grand_total = round($grandTotalValue);

    $record->save();

    $updated++;

    if ($updated <= 10) {
        echo "✓ Baris $rowNum: $nomorKontainer (Periode $periode)\n";
        echo "  DPP: Rp " . number_format($dppValue, 0, ',', '.') . " (dari CSV)\n";
        echo "  Adjustment: Rp " . number_format($adjustmentValue, 0, ',', '.') . "\n";
        echo "  PPN: Rp " . number_format($ppnValue, 0, ',', '.') . " (dari CSV)\n";
        echo "  PPH: Rp " . number_format($pphValue, 0, ',', '.') . " (dari CSV)\n";
        echo "  Grand Total: Rp " . number_format($grandTotalValue, 0, ',', '.') . " (dari CSV)\n\n";
    }
}

fclose($handle);

echo "\n=== Ringkasan Update ===\n";
echo "Total updated: $updated\n";
echo "Not found in database: $notFound\n";

if (!empty($errors)) {
    echo "\n=== Errors ===\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}

// Verifikasi hasil
echo "\n=== Verifikasi Sample Data ===\n";
$samples = DaftarTagihanKontainerSewa::orderBy('id')->limit(10)->get();

foreach ($samples as $sample) {
    echo "\n- {$sample->vendor} - {$sample->nomor_kontainer} ({$sample->size}ft)\n";
    echo "  Periode: {$sample->periode}, Masa: {$sample->masa}\n";
    echo "  DPP: Rp " . number_format($sample->dpp ?? 0, 0, ',', '.') . "\n";
    echo "  Adjustment: Rp " . number_format($sample->adjustment ?? 0, 0, ',', '.') . "\n";
    echo "  PPN: Rp " . number_format($sample->ppn ?? 0, 0, ',', '.') . "\n";
    echo "  PPH: Rp " . number_format($sample->pph ?? 0, 0, ',', '.') . "\n";
    echo "  Grand Total: Rp " . number_format($sample->grand_total ?? 0, 0, ',', '.') . "\n";
}

// Hitung total keseluruhan
$totalRecords = DaftarTagihanKontainerSewa::count();
$totalDPP = DaftarTagihanKontainerSewa::sum('dpp');
$totalAdjustment = DaftarTagihanKontainerSewa::sum('adjustment');
$totalPPN = DaftarTagihanKontainerSewa::sum('ppn');
$totalPPH = DaftarTagihanKontainerSewa::sum('pph');
$totalGrandTotal = DaftarTagihanKontainerSewa::sum('grand_total');

echo "\n=== Total Keseluruhan ($totalRecords records) ===\n";
echo "Total DPP: Rp " . number_format($totalDPP, 0, ',', '.') . "\n";
echo "Total Adjustment: Rp " . number_format($totalAdjustment, 0, ',', '.') . "\n";
echo "Total PPN: Rp " . number_format($totalPPN, 0, ',', '.') . "\n";
echo "Total PPH: Rp " . number_format($totalPPH, 0, ',', '.') . "\n";
echo "Total Grand Total: Rp " . number_format($totalGrandTotal, 0, ',', '.') . "\n";

echo "\n=== Update selesai ===\n";
