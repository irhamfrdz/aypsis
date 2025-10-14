<?php

/**
 * Script Analisis Perbedaan Data CSV vs Database
 *
 * Script ini akan membandingkan data dari file CSV export dengan data di database
 * untuk menemukan perbedaan dan inkonsistensi
 */

// Pastikan script dijalankan dari direktori Laravel
if (!file_exists('artisan')) {
    die("Error: Script harus dijalankan dari root direktori Laravel\n");
}

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== ANALISIS PERBEDAAN DATA CSV vs DATABASE ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

// Path ke file CSV yang akan dianalisis
$csvPath = 'c:\Users\amanda\Downloads\export_tagihan_kontainer_sewa_2025-10-14_100640.csv';

if (!file_exists($csvPath)) {
    echo "❌ File CSV tidak ditemukan: {$csvPath}\n";
    echo "Pastikan file CSV sudah berada di lokasi yang benar.\n";
    exit(1);
}

echo "📁 File CSV: {$csvPath}\n";
echo "📊 Memulai analisis...\n\n";

try {
    // Baca file CSV
    $csvData = [];
    $handle = fopen($csvPath, 'r');

    if (!$handle) {
        throw new Exception("Gagal membuka file CSV");
    }

    // Baca header
    $header = fgetcsv($handle, 1000, ';');

    // Clean BOM jika ada
    if (!empty($header[0])) {
        $header[0] = preg_replace('/^\x{FEFF}/u', '', $header[0]);
    }

    echo "📋 Header CSV: [" . implode(', ', $header) . "]\n\n";

    $rowCount = 0;
    while (($row = fgetcsv($handle, 1000, ';')) !== false) {
        if (count($row) == count($header)) {
            $data = array_combine($header, $row);
            $csvData[] = $data;
            $rowCount++;
        }
    }
    fclose($handle);

    echo "✅ Berhasil membaca {$rowCount} baris dari CSV\n\n";

    // Analisis struktur data CSV
    echo "🔍 ANALISIS STRUKTUR DATA CSV:\n";

    // Group by container
    $csvByContainer = [];
    foreach ($csvData as $row) {
        $container = trim($row['Nomor Kontainer'] ?? '');
        if (!empty($container)) {
            if (!isset($csvByContainer[$container])) {
                $csvByContainer[$container] = [];
            }
            $csvByContainer[$container][] = $row;
        }
    }

    echo "   Jumlah kontainer unik: " . count($csvByContainer) . "\n";
    echo "   Total baris data: " . $rowCount . "\n";

    // Analisis vendor
    $vendors = [];
    foreach ($csvData as $row) {
        $vendor = trim($row['Vendor'] ?? '');
        if (!empty($vendor)) {
            $vendors[$vendor] = ($vendors[$vendor] ?? 0) + 1;
        }
    }
    echo "   Vendor: " . implode(', ', array_keys($vendors)) . "\n";
    foreach ($vendors as $vendor => $count) {
        echo "     - {$vendor}: {$count} baris\n";
    }

    // Analisis periode
    $periods = [];
    foreach ($csvData as $row) {
        $periode = trim($row['Periode'] ?? '');
        if (!empty($periode)) {
            $periods[$periode] = ($periods[$periode] ?? 0) + 1;
        }
    }
    ksort($periods);
    echo "   Periode range: " . min(array_keys($periods)) . " - " . max(array_keys($periods)) . "\n";

    echo "\n";

    // Bandingkan dengan database
    echo "🔍 PERBANDINGAN DENGAN DATABASE:\n\n";

    $dbIssues = [];
    $csvIssues = [];
    $matches = 0;
    $mismatches = 0;

    foreach ($csvByContainer as $containerNumber => $csvRows) {
        echo "📦 Container: {$containerNumber}\n";

        // Ambil data dari database
        $dbRows = DaftarTagihanKontainerSewa::where('nomor_kontainer', $containerNumber)
            ->orderBy('periode')
            ->get()
            ->toArray();

        echo "   CSV: " . count($csvRows) . " periode | DB: " . count($dbRows) . " periode\n";

        if (count($csvRows) != count($dbRows)) {
            $dbIssues[] = "Container {$containerNumber}: Jumlah periode berbeda (CSV: " . count($csvRows) . ", DB: " . count($dbRows) . ")";
        }

        // Bandingkan periode by periode
        foreach ($csvRows as $csvRow) {
            $periode = intval($csvRow['Periode'] ?? 0);

            // Cari matching row di database
            $dbRow = collect($dbRows)->firstWhere('periode', $periode);

            if (!$dbRow) {
                $csvIssues[] = "Container {$containerNumber} Periode {$periode}: Ada di CSV tapi tidak ada di DB";
                continue;
            }

            // Bandingkan field-field penting
            $differences = [];

            // Vendor
            $csvVendor = trim($csvRow['Vendor'] ?? '');
            $dbVendor = trim($dbRow['vendor'] ?? '');
            if ($csvVendor != $dbVendor) {
                $differences[] = "Vendor (CSV: {$csvVendor}, DB: {$dbVendor})";
            }

            // Size
            $csvSize = trim($csvRow['Size'] ?? '');
            $dbSize = trim($dbRow['size'] ?? '');
            if ($csvSize != $dbSize) {
                $differences[] = "Size (CSV: {$csvSize}, DB: {$dbSize})";
            }

            // DPP
            $csvDpp = floatval(str_replace(',', '', $csvRow['DPP'] ?? 0));
            $dbDpp = floatval($dbRow['dpp'] ?? 0);
            if (abs($csvDpp - $dbDpp) > 0.01) {
                $differences[] = "DPP (CSV: " . number_format($csvDpp, 2) . ", DB: " . number_format($dbDpp, 2) . ")";
            }

            // Adjustment
            $csvAdjustment = floatval(str_replace(',', '', $csvRow['Adjustment'] ?? 0));
            $dbAdjustment = floatval($dbRow['adjustment'] ?? 0);
            if (abs($csvAdjustment - $dbAdjustment) > 0.01) {
                $differences[] = "Adjustment (CSV: " . number_format($csvAdjustment, 2) . ", DB: " . number_format($dbAdjustment, 2) . ")";
            }

            // Grand Total
            $csvGrandTotal = floatval(str_replace(',', '', $csvRow['Grand Total'] ?? 0));
            $dbGrandTotal = floatval($dbRow['grand_total'] ?? 0);
            if (abs($csvGrandTotal - $dbGrandTotal) > 0.01) {
                $differences[] = "Grand Total (CSV: " . number_format($csvGrandTotal, 2) . ", DB: " . number_format($dbGrandTotal, 2) . ")";
            }

            // Tanggal
            $csvTglAwal = $csvRow['Tanggal Awal'] ?? '';
            $dbTglAwal = $dbRow['tanggal_awal'] ?? '';
            if (!empty($csvTglAwal) && !empty($dbTglAwal)) {
                try {
                    $csvDate = Carbon::createFromFormat('d-m-Y', $csvTglAwal)->format('Y-m-d');
                    if ($csvDate != $dbTglAwal) {
                        $differences[] = "Tanggal Awal (CSV: {$csvTglAwal}, DB: {$dbTglAwal})";
                    }
                } catch (Exception $e) {
                    $differences[] = "Tanggal Awal format error (CSV: {$csvTglAwal})";
                }
            }

            if (empty($differences)) {
                $matches++;
                echo "   ✅ Periode {$periode}: Match\n";
            } else {
                $mismatches++;
                echo "   ❌ Periode {$periode}: " . implode(', ', $differences) . "\n";
            }
        }

        // Cek apakah ada data di DB yang tidak ada di CSV
        foreach ($dbRows as $dbRow) {
            $periode = $dbRow['periode'];
            $csvRow = collect($csvRows)->firstWhere('Periode', $periode);
            if (!$csvRow) {
                $dbIssues[] = "Container {$containerNumber} Periode {$periode}: Ada di DB tapi tidak ada di CSV";
            }
        }

        echo "\n";
    }

    // Summary
    echo "=== SUMMARY PERBANDINGAN ===\n";
    echo "✅ Data yang cocok: {$matches}\n";
    echo "❌ Data yang berbeda: {$mismatches}\n";
    echo "📋 Total kontainer dianalisis: " . count($csvByContainer) . "\n\n";

    if (!empty($csvIssues)) {
        echo "🔍 ISSUES DARI CSV:\n";
        foreach ($csvIssues as $issue) {
            echo "   - {$issue}\n";
        }
        echo "\n";
    }

    if (!empty($dbIssues)) {
        echo "🗄️ ISSUES DARI DATABASE:\n";
        foreach ($dbIssues as $issue) {
            echo "   - {$issue}\n";
        }
        echo "\n";
    }

    // Analisis pola data
    echo "📊 ANALISIS POLA DATA:\n";

    // Cek apakah ada pola dalam perhitungan
    $calculationIssues = [];
    foreach ($csvData as $row) {
        $dpp = floatval(str_replace(',', '', $row['DPP'] ?? 0));
        $adjustment = floatval(str_replace(',', '', $row['Adjustment'] ?? 0));
        $ppn = floatval(str_replace(',', '', $row['PPN'] ?? 0));
        $pph = floatval(str_replace(',', '', $row['PPH'] ?? 0));
        $grandTotal = floatval(str_replace(',', '', $row['Grand Total'] ?? 0));

        // Hitung ulang
        $adjustedDpp = $dpp + $adjustment;
        $calculatedPpn = $adjustedDpp * 0.11;
        $calculatedPph = $adjustedDpp * 0.02;
        $calculatedGrandTotal = $adjustedDpp + $calculatedPpn - $calculatedPph;

        $container = $row['Nomor Kontainer'] ?? '';
        $periode = $row['Periode'] ?? '';

        // Cek PPN
        if (abs($ppn - $calculatedPpn) > 0.01) {
            $calculationIssues[] = "{$container} P{$periode}: PPN tidak sesuai (Expected: " . number_format($calculatedPpn, 2) . ", Actual: " . number_format($ppn, 2) . ")";
        }

        // Cek PPH
        if (abs($pph - $calculatedPph) > 0.01) {
            $calculationIssues[] = "{$container} P{$periode}: PPH tidak sesuai (Expected: " . number_format($calculatedPph, 2) . ", Actual: " . number_format($calculatedPph, 2) . ")";
        }

        // Cek Grand Total
        if (abs($grandTotal - $calculatedGrandTotal) > 0.01) {
            $calculationIssues[] = "{$container} P{$periode}: Grand Total tidak sesuai (Expected: " . number_format($calculatedGrandTotal, 2) . ", Actual: " . number_format($grandTotal, 2) . ")";
        }
    }

    if (!empty($calculationIssues)) {
        echo "⚠️ Issues Perhitungan (PPN=11%, PPH=2%):\n";
        foreach (array_slice($calculationIssues, 0, 10) as $issue) {
            echo "   - {$issue}\n";
        }
        if (count($calculationIssues) > 10) {
            echo "   ... dan " . (count($calculationIssues) - 10) . " issues lainnya\n";
        }
    } else {
        echo "✅ Semua perhitungan PPN/PPH sudah sesuai\n";
    }

    echo "\n🎉 Analisis selesai!\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
