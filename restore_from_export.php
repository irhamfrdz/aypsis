<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\NomorTerakhir;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== RESTORE DATA DARI EXPORT CSV ===\n\n";

$csvFile = 'C:\\Users\\amanda\\Downloads\\export_tagihan_kontainer_sewa_2025-10-14_090836.csv';

if (!file_exists($csvFile)) {
    echo "❌ File tidak ditemukan: $csvFile\n";
    exit(1);
}

try {
    DB::beginTransaction();

    // Baca CSV
    $handle = fopen($csvFile, 'r');

    // Skip BOM if exists
    $first_line = fgets($handle);
    if (substr($first_line, 0, 3) == "\xEF\xBB\xBF") {
        $first_line = substr($first_line, 3);
    }
    rewind($handle);

    // Get header
    $header = fgetcsv($handle, 0, ';');

    // Clean header
    $header = array_map(function($h) {
        return trim(str_replace(["\xEF\xBB\xBF", "\x00"], '', $h));
    }, $header);

    echo "📋 Header CSV Export:\n";
    foreach ($header as $i => $col) {
        echo "   $i: $col\n";
    }
    echo "\n";

    // Map kolom yang diperlukan
    $headerMap = array_flip($header);

    $dataRows = [];
    $rowNumber = 1;
    $totalAmount = 0;
    $errors = [];
    $duplicates = [];

    // Baca semua data
    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        $rowNumber++;

        if (empty(array_filter($row))) continue; // Skip empty rows

        try {
            $group = trim($row[$headerMap['Group']] ?? '');
            $vendor = trim($row[$headerMap['Vendor']] ?? '');
            $nomorKontainer = trim($row[$headerMap['Nomor Kontainer']] ?? '');
            $size = trim($row[$headerMap['Size']] ?? '');
            $tanggalAwal = trim($row[$headerMap['Tanggal Awal']] ?? '');
            $tanggalAkhir = trim($row[$headerMap['Tanggal Akhir']] ?? '');
            $periode = trim($row[$headerMap['Periode']] ?? '');
            $masa = trim($row[$headerMap['Masa']] ?? '');
            $tarif = trim($row[$headerMap['Tarif']] ?? '');
            $status = trim($row[$headerMap['Status']] ?? '');

            // Parse numeric values
            $dppRaw = trim($row[$headerMap['DPP']] ?? '0');
            $adjustmentRaw = trim($row[$headerMap['Adjustment']] ?? '0');
            $dppNilaiLainRaw = trim($row[$headerMap['DPP Nilai Lain']] ?? '0');
            $ppnRaw = trim($row[$headerMap['PPN']] ?? '0');
            $pphRaw = trim($row[$headerMap['PPH']] ?? '0');
            $grandTotalRaw = trim($row[$headerMap['Grand Total']] ?? '0');

            // Convert to float
            $dpp = (float) str_replace(',', '.', str_replace('.', '', $dppRaw));
            $adjustment = (float) str_replace(',', '.', str_replace('.', '', $adjustmentRaw));
            $dppNilaiLain = (float) str_replace(',', '.', str_replace('.', '', $dppNilaiLainRaw));
            $ppn = (float) str_replace(',', '.', str_replace('.', '', $ppnRaw));
            $pph = (float) str_replace(',', '.', str_replace('.', '', $pphRaw));
            $grandTotal = (float) str_replace(',', '.', str_replace('.', '', $grandTotalRaw));

            // Status pranota dan pranota_id
            $statusPranota = trim($row[$headerMap['Status Pranota']] ?? '');
            $pranotaId = trim($row[$headerMap['Pranota ID']] ?? '');
            $pranotaId = !empty($pranotaId) && is_numeric($pranotaId) ? (int)$pranotaId : null;

            // Validasi data wajib
            if (empty($nomorKontainer) || empty($vendor)) {
                $errors[] = "Baris $rowNumber: Data tidak lengkap (nomor kontainer atau vendor kosong)";
                continue;
            }

            // Parse tanggal
            $tanggalAwalParsed = null;
            $tanggalAkhirParsed = null;

            if (!empty($tanggalAwal)) {
                try {
                    $tanggalAwalParsed = Carbon::parse($tanggalAwal)->format('Y-m-d');
                } catch (Exception $e) {
                    $tanggalAwalParsed = null;
                }
            }

            if (!empty($tanggalAkhir)) {
                try {
                    $tanggalAkhirParsed = Carbon::parse($tanggalAkhir)->format('Y-m-d');
                } catch (Exception $e) {
                    $tanggalAkhirParsed = null;
                }
            }

            // Cek apakah record sudah ada
            $existing = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)
                ->where('vendor', $vendor)
                ->where('periode', (int)$periode)
                ->first();

            if ($existing) {
                $duplicates[] = $nomorKontainer;
                continue;
            }

            // Simpan data untuk insert ke database
            $dataRows[] = [
                'nomor_kontainer' => $nomorKontainer,
                'vendor' => $vendor,
                'size' => $size,
                'periode' => (int)$periode,
                'tanggal_awal' => $tanggalAwalParsed,
                'tanggal_akhir' => $tanggalAkhirParsed,
                'group' => $group,
                'masa' => $masa,
                'tarif' => $tarif,
                'tarif_nominal' => $dpp, // Use DPP as tarif_nominal
                'dpp' => $dpp,
                'adjustment' => $adjustment,
                'dpp_nilai_lain' => $dppNilaiLain,
                'ppn' => $ppn,
                'pph' => $pph,
                'grand_total' => $grandTotal,
                'status' => $status ?: 'active',
                'status_pranota' => !empty($statusPranota) ? $statusPranota : null,
                'pranota_id' => $pranotaId,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $totalAmount += $grandTotal;

        } catch (Exception $e) {
            $errors[] = "Baris $rowNumber: Error parsing - " . $e->getMessage();
        }
    }

    fclose($handle);

    echo "✅ Data berhasil dibaca: " . count($dataRows) . " records\n";
    echo "🔄 Data duplikat di-skip: " . count($duplicates) . " records\n";
    echo "💰 Total amount: Rp " . number_format($totalAmount, 2, ',', '.') . "\n\n";

    if (!empty($errors)) {
        echo "⚠️ Errors (10 pertama):\n";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "   - $error\n";
        }
        echo "\n";
    }

    if (empty($dataRows)) {
        echo "❌ Tidak ada data baru untuk diimport\n";
        exit(0);
    }

    // Insert data ke tabel daftar_tagihan_kontainer_sewa
    echo "🔄 Memasukkan data ke tabel daftar_tagihan_kontainer_sewa...\n";

    $insertedIds = [];
    $insertCount = 0;

    foreach ($dataRows as $data) {
        try {
            $id = DB::table('daftar_tagihan_kontainer_sewa')->insertGetId($data);
            $insertedIds[] = $id;
            $insertCount++;

            if ($insertCount % 100 == 0) {
                echo "   Progress: $insertCount/" . count($dataRows) . " records...\n";
            }
        } catch (Exception $e) {
            $errors[] = "Insert error untuk {$data['nomor_kontainer']}: " . $e->getMessage();
        }
    }

    echo "✅ Berhasil insert $insertCount tagihan\n\n";

    // Analisis data yang memiliki pranota_id
    $dataWithPranotaId = array_filter($dataRows, function($data) {
        return !empty($data['pranota_id']);
    });

    if (!empty($dataWithPranotaId)) {
        echo "🔍 Ditemukan " . count($dataWithPranotaId) . " records yang sudah terhubung dengan pranota\n";

        // Group by pranota_id
        $pranotaGroups = [];
        foreach ($dataWithPranotaId as $data) {
            $pranotaId = $data['pranota_id'];
            if (!isset($pranotaGroups[$pranotaId])) {
                $pranotaGroups[$pranotaId] = [];
            }
            $pranotaGroups[$pranotaId][] = $data;
        }

        echo "📊 Pranota yang perlu di-restore: " . count($pranotaGroups) . "\n";
    }

    DB::commit();

    echo "\n🎉 RESTORE BERHASIL!\n";
    echo "==================\n";
    echo "📊 Total Data Diimport: $insertCount\n";
    echo "🔄 Data Duplikat Di-skip: " . count($duplicates) . "\n";
    echo "💰 Total Amount: Rp " . number_format($totalAmount, 2, ',', '.') . "\n";
    echo "⚠️ Total Errors: " . count($errors) . "\n\n";

    if (!empty($errors)) {
        echo "📋 Sample Errors (5 pertama):\n";
        foreach (array_slice($errors, 0, 5) as $error) {
            echo "   - $error\n";
        }
    }

} catch (Exception $e) {
    DB::rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✅ Selesai!\n";
