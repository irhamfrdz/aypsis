#!/usr/bin/env php
<?php
/**
 * Script untuk memperbaiki DPP di CSV berdasarkan jumlah hari aktual
 */

use Carbon\Carbon;

require_once __DIR__ . '/vendor/autoload.php';

// Path file CSV yang akan diperbaiki
$csvFile = 'export_tagihan_kontainer_sewa_2025-10-03_110147_FIXED.csv';
$inputFile = 'export_tagihan_kontainer_sewa_2025-10-03_110147 (1).csv';

echo "🔧 PERBAIKAN CSV - DPP BERDASARKAN JUMLAH HARI AKTUAL\n";
echo "====================================================\n\n";

// Baca CSV asli
if (!file_exists($inputFile)) {
    echo "❌ File tidak ditemukan: $inputFile\n";
    exit(1);
}

$csvContent = file_get_contents($inputFile);
$lines = explode("\n", $csvContent);

if (empty($lines)) {
    echo "❌ File CSV kosong\n";
    exit(1);
}

// Parse header
$header = str_getcsv($lines[0], ';');
$headerMap = array_flip($header);

// Pastikan kolom yang dibutuhkan ada
$requiredColumns = ['Vendor', 'Size', 'Tanggal Awal', 'Tanggal Akhir', 'DPP', 'PPN', 'PPH', 'Grand Total'];
foreach ($requiredColumns as $col) {
    if (!isset($headerMap[$col])) {
        echo "❌ Kolom '$col' tidak ditemukan dalam CSV\n";
        exit(1);
    }
}

echo "✅ Header CSV berhasil dibaca\n";
echo "📋 Kolom yang akan diperbaiki: DPP, PPN, PPH, Grand Total\n\n";

$fixedLines = [];
$fixedLines[] = $lines[0]; // Header tetap sama

$totalFixed = 0;
$errors = [];

// Process setiap baris
for ($i = 1; $i < count($lines); $i++) {
    $line = trim($lines[$i]);
    if (empty($line)) continue;

    try {
        $data = str_getcsv($line, ';');

        if (count($data) < count($header)) {
            $errors[] = "Baris $i: Data tidak lengkap";
            continue;
        }

        // Ambil data yang diperlukan
        $vendor = trim($data[$headerMap['Vendor']]);
        $size = trim($data[$headerMap['Size']]);
        $tanggalAwal = trim($data[$headerMap['Tanggal Awal']]);
        $tanggalAkhir = trim($data[$headerMap['Tanggal Akhir']]);

        // Parse tanggal
        $startDate = Carbon::createFromFormat('d-m-Y', $tanggalAwal);
        $endDate = Carbon::createFromFormat('d-m-Y', $tanggalAkhir);

        if (!$startDate || !$endDate) {
            $errors[] = "Baris $i: Format tanggal tidak valid ($tanggalAwal - $tanggalAkhir)";
            continue;
        }

        // Hitung jumlah hari aktual
        $jumlahHari = $startDate->diffInDays($endDate) + 1;

        // Tentukan tarif per hari berdasarkan vendor dan size
        $tarifPerHari = 0;
        if ($vendor === 'DPE') {
            $tarifPerHari = ($size == '20') ? 25000 : 35000;
        } elseif ($vendor === 'ZONA') {
            $tarifPerHari = ($size == '20') ? 20000 : 30000;
        }

        if ($tarifPerHari == 0) {
            $errors[] = "Baris $i: Vendor '$vendor' atau size '$size' tidak dikenali";
            continue;
        }

        // Hitung nilai baru
        $dppBaru = $tarifPerHari * $jumlahHari;
        $ppnBaru = round($dppBaru * 0.11, 2);
        $pphBaru = round($dppBaru * 0.02, 2);
        $grandTotalBaru = round($dppBaru + $ppnBaru - $pphBaru, 2);

        // Update data
        $data[$headerMap['DPP']] = number_format($dppBaru, 2, '.', '');
        $data[$headerMap['PPN']] = number_format($ppnBaru, 2, '.', '');
        $data[$headerMap['PPH']] = number_format($pphBaru, 2, '.', '');
        $data[$headerMap['Grand Total']] = number_format($grandTotalBaru, 2, '.', '');

        // Update DPP Nilai Lain juga (11/12 dari DPP)
        if (isset($headerMap['DPP Nilai Lain'])) {
            $dppNilaiLain = round($dppBaru * 11 / 12, 2);
            $data[$headerMap['DPP Nilai Lain']] = number_format($dppNilaiLain, 2, '.', '');
        }

        // Buat baris CSV baru
        $fixedLine = '';
        foreach ($data as $field) {
            $fixedLine .= '"' . str_replace('"', '""', $field) . '";';
        }
        $fixedLine = rtrim($fixedLine, ';');

        $fixedLines[] = $fixedLine;
        $totalFixed++;

        // Log perubahan untuk beberapa baris pertama
        if ($totalFixed <= 5) {
            echo "📝 Baris $i - $vendor $size ($tanggalAwal s/d $tanggalAkhir):\n";
            echo "   Jumlah hari: $jumlahHari hari\n";
            echo "   DPP: Rp " . number_format($dppBaru, 0, '.', ',') . " (Rp " . number_format($tarifPerHari, 0, '.', ',') . " × $jumlahHari)\n";
            echo "   PPN: Rp " . number_format($ppnBaru, 0, '.', ',') . "\n";
            echo "   PPH: Rp " . number_format($pphBaru, 0, '.', ',') . "\n";
            echo "   Grand Total: Rp " . number_format($grandTotalBaru, 0, '.', ',') . "\n\n";
        }

    } catch (Exception $e) {
        $errors[] = "Baris $i: Error - " . $e->getMessage();
        continue;
    }
}

// Tulis file CSV yang sudah diperbaiki
file_put_contents($csvFile, implode("\n", $fixedLines));

echo "🎉 PERBAIKAN SELESAI!\n";
echo "=====================\n";
echo "✅ Total baris diperbaiki: $totalFixed\n";
echo "📄 File output: $csvFile\n\n";

if (!empty($errors)) {
    echo "⚠️ ERRORS:\n";
    foreach ($errors as $error) {
        echo "   - $error\n";
    }
    echo "\n";
}

echo "🔍 RINGKASAN PERBAIKAN:\n";
echo "• DPP sekarang dihitung: Tarif per hari × Jumlah hari aktual\n";
echo "• Tarif DPE 20ft: Rp 25.000/hari\n";
echo "• Tarif DPE 40ft: Rp 35.000/hari\n";
echo "• Tarif ZONA 20ft: Rp 20.000/hari\n";
echo "• Tarif ZONA 40ft: Rp 30.000/hari\n";
echo "• PPN: 11% dari DPP\n";
echo "• PPH: 2% dari DPP\n";
echo "• Grand Total: DPP + PPN - PPH\n\n";

echo "✅ Silakan gunakan file: $csvFile\n";
