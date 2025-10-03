<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Carbon\Carbon;

echo "=== Update Adjustment dari CSV ===\n\n";

$csvFile = 'C:\Users\amanda\Downloads\Tagihan Kontainer Sewa DPE.csv';
$handle = fopen($csvFile, 'r');

if (!$handle) {
    die("Error: Tidak dapat membuka file CSV\n");
}

// Baca dan skip header
$header = fgetcsv($handle, 0, ';');

$updated = 0;
$notFound = 0;
$skipped = 0;
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
    $adjustment = trim($data[12]);

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

    // Parse adjustment value
    $adjustmentValue = 0;
    if ($adjustment && $adjustment !== '-' && trim($adjustment) !== '') {
        // Remove formatting: -112.500,00 -> -112500.00
        $adjustmentCleaned = str_replace(['.', ','], ['', '.'], $adjustment);
        $adjustmentValue = (float) $adjustmentCleaned;
    }

    // Update adjustment
    $record->adjustment = $adjustmentValue;

    // Recalculate financial data dengan adjustment
    $dpp = $record->dpp; // DPP sudah benar dari perhitungan sebelumnya
    $dppAfterAdjustment = $dpp + $adjustmentValue; // DPP setelah adjustment

    // Hitung ulang PPN dan PPH berdasarkan DPP after adjustment
    $ppn = $dppAfterAdjustment * 0.11; // 11%
    $pph = $dppAfterAdjustment * 0.02; // 2%
    $grandTotal = $dppAfterAdjustment + $ppn - $pph;

    // Update semua nilai
    $record->ppn = round($ppn);
    $record->pph = round($pph);
    $record->grand_total = round($grandTotal);

    $record->save();

    $updated++;

    if ($updated <= 10) {
        echo "✓ Baris $rowNum: $nomorKontainer (Periode $periode)\n";
        echo "  Adjustment: Rp " . number_format($adjustmentValue, 0, ',', '.') . "\n";
        echo "  DPP: Rp " . number_format($dpp, 0, ',', '.') . "\n";
        echo "  DPP After Adjustment: Rp " . number_format($dppAfterAdjustment, 0, ',', '.') . "\n";
        echo "  PPN (11%): Rp " . number_format($ppn, 0, ',', '.') . "\n";
        echo "  PPH (2%): Rp " . number_format($pph, 0, ',', '.') . "\n";
        echo "  Grand Total: Rp " . number_format($grandTotal, 0, ',', '.') . "\n\n";
    }
}

fclose($handle);

echo "\n=== Ringkasan Update ===\n";
echo "Total updated: $updated\n";
echo "Not found in database: $notFound\n";
echo "Skipped: $skipped\n";

if (!empty($errors)) {
    echo "\n=== Errors ===\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}

// Verifikasi hasil
echo "\n=== Verifikasi Sample Data ===\n";
$samples = DaftarTagihanKontainerSewa::whereNotNull('adjustment')
    ->where('adjustment', '!=', 0)
    ->limit(5)
    ->get();

foreach ($samples as $sample) {
    echo "\n- {$sample->vendor} - {$sample->nomor_kontainer} ({$sample->size}ft)\n";
    echo "  Periode: {$sample->periode}\n";
    echo "  Adjustment: Rp " . number_format($sample->adjustment, 0, ',', '.') . "\n";
    echo "  DPP: Rp " . number_format($sample->dpp, 0, ',', '.') . "\n";
    echo "  PPN: Rp " . number_format($sample->ppn, 0, ',', '.') . "\n";
    echo "  PPH: Rp " . number_format($sample->pph, 0, ',', '.') . "\n";
    echo "  Grand Total: Rp " . number_format($sample->grand_total, 0, ',', '.') . "\n";
}

echo "\n=== Update selesai ===\n";
