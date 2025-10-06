#!/usr/bin/env php
<?php
/**
 * Script sederhana untuk memperbaiki DPP di CSV berdasarkan jumlah hari aktual
 * Tidak memerlukan dependencies - bisa langsung dijalankan
 */

echo "🔧 PERBAIKAN CSV - DPP BERDASARKAN JUMLAH HARI AKTUAL\n";
echo "====================================================\n\n";

// Function untuk menghitung selisih hari
function hitungSelisihHari($tanggalAwal, $tanggalAkhir) {
    // Format: dd-mm-yyyy
    $parts1 = explode('-', $tanggalAwal);
    $parts2 = explode('-', $tanggalAkhir);

    if (count($parts1) != 3 || count($parts2) != 3) {
        throw new Exception("Format tanggal tidak valid");
    }

    $date1 = new DateTime($parts1[2] . '-' . $parts1[1] . '-' . $parts1[0]);
    $date2 = new DateTime($parts2[2] . '-' . $parts2[1] . '-' . $parts2[0]);

    $diff = $date1->diff($date2);
    return $diff->days + 1; // +1 untuk include end date
}

// Path file
$inputFile = 'export_tagihan_kontainer_sewa_2025-10-03_110147 (1).csv';
$outputFile = 'export_tagihan_kontainer_sewa_FIXED.csv';

// Baca file
if (!file_exists($inputFile)) {
    echo "❌ File tidak ditemukan: $inputFile\n";
    echo "💡 Pastikan file CSV ada di direktori yang sama dengan script ini\n";
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

// Map posisi kolom
$columnMap = [];
foreach ($header as $index => $columnName) {
    $columnMap[trim(str_replace('"', '', $columnName))] = $index;
}

// Cek kolom yang dibutuhkan
$requiredColumns = ['Vendor', 'Size', 'Tanggal Awal', 'Tanggal Akhir', 'DPP', 'PPN', 'PPH', 'Grand Total'];
foreach ($requiredColumns as $col) {
    if (!isset($columnMap[$col])) {
        echo "❌ Kolom '$col' tidak ditemukan dalam CSV\n";
        echo "📋 Kolom yang tersedia: " . implode(', ', array_keys($columnMap)) . "\n";
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

        // Bersihkan quotes dari data
        $data = array_map(function($item) {
            return trim(str_replace('"', '', $item));
        }, $data);

        if (count($data) < count($header)) {
            $errors[] = "Baris $i: Data tidak lengkap (" . count($data) . " kolom, expected " . count($header) . ")";
            continue;
        }

        // Ambil data yang diperlukan
        $vendor = trim($data[$columnMap['Vendor']]);
        $size = trim($data[$columnMap['Size']]);
        $tanggalAwal = trim($data[$columnMap['Tanggal Awal']]);
        $tanggalAkhir = trim($data[$columnMap['Tanggal Akhir']]);

        // Hitung jumlah hari aktual
        try {
            $jumlahHari = hitungSelisihHari($tanggalAwal, $tanggalAkhir);
        } catch (Exception $e) {
            $errors[] = "Baris $i: Error tanggal ($tanggalAwal - $tanggalAkhir): " . $e->getMessage();
            continue;
        }

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

        // Ambil DPP lama untuk perbandingan
        $dppLama = (float) $data[$columnMap['DPP']];

        // Update data
        $data[$columnMap['DPP']] = number_format($dppBaru, 2, '.', '');
        $data[$columnMap['PPN']] = number_format($ppnBaru, 2, '.', '');
        $data[$columnMap['PPH']] = number_format($pphBaru, 2, '.', '');
        $data[$columnMap['Grand Total']] = number_format($grandTotalBaru, 2, '.', '');

        // Update DPP Nilai Lain juga (11/12 dari DPP)
        if (isset($columnMap['DPP Nilai Lain'])) {
            $dppNilaiLain = round($dppBaru * 11 / 12, 2);
            $data[$columnMap['DPP Nilai Lain']] = number_format($dppNilaiLain, 2, '.', '');
        }

        // Buat baris CSV baru dengan quotes
        $fixedLine = '';
        foreach ($data as $field) {
            $fixedLine .= '"' . str_replace('"', '""', $field) . '";';
        }
        $fixedLine = rtrim($fixedLine, ';');

        $fixedLines[] = $fixedLine;
        $totalFixed++;

        // Log perubahan untuk beberapa baris pertama
        if ($totalFixed <= 10) {
            $kontainer = isset($columnMap['Nomor Kontainer']) ? $data[$columnMap['Nomor Kontainer']] : 'N/A';
            $periode = isset($columnMap['Periode']) ? $data[$columnMap['Periode']] : 'N/A';

            echo "📝 Baris $i - $kontainer P$periode ($vendor $size):\n";
            echo "   Tanggal: $tanggalAwal s/d $tanggalAkhir ($jumlahHari hari)\n";
            echo "   DPP Lama: Rp " . number_format($dppLama, 0, '.', ',') . "\n";
            echo "   DPP Baru: Rp " . number_format($dppBaru, 0, '.', ',') . " (Rp " . number_format($tarifPerHari, 0, '.', ',') . " × $jumlahHari)\n";
            echo "   Selisih: Rp " . number_format($dppBaru - $dppLama, 0, '.', ',') . "\n\n";
        }

    } catch (Exception $e) {
        $errors[] = "Baris $i: Error - " . $e->getMessage();
        continue;
    }
}

// Tulis file CSV yang sudah diperbaiki
file_put_contents($outputFile, implode("\n", $fixedLines));

echo "🎉 PERBAIKAN SELESAI!\n";
echo "=====================\n";
echo "✅ Total baris diperbaiki: $totalFixed\n";
echo "📄 File input: $inputFile\n";
echo "📄 File output: $outputFile\n\n";

if (!empty($errors)) {
    echo "⚠️ ERRORS ($" . count($errors) . " errors):\n";
    foreach (array_slice($errors, 0, 5) as $error) {
        echo "   - $error\n";
    }
    if (count($errors) > 5) {
        echo "   ... dan " . (count($errors) - 5) . " error lainnya\n";
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

echo "✅ File CSV sudah diperbaiki: $outputFile\n";
echo "📋 Silakan gunakan file tersebut untuk import ulang\n";
