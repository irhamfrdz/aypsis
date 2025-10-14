<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\NomorTerakhir;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== IMPORT CSV ZONA KE PRANOTA ===\n\n";

$csvFile = 'C:\\Users\\amanda\\Downloads\\Zona.csv';

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

    echo "📋 Header CSV:\n";
    foreach ($header as $i => $col) {
        echo "   $i: $col\n";
    }
    echo "\n";

    // Map kolom yang diperlukan (sesuai dengan struktur CSV Zona)
    $headerMap = array_flip($header);

    // Mapping kolom CSV Zona ke struktur yang diperlukan
    $columnMapping = [
        'nomor_kontainer' => 'Kontainer',
        'vendor' => 'ZONA', // Fixed value
        'ukuran' => 'Ukuran',
        'periode' => 'Periode',
        'tanggal_awal' => 'Awal',
        'tanggal_akhir' => 'Akhir',
        'dpp' => 'DPP',
        'ppn' => 'ppn',
        'pph' => 'pph',
        'grand_total' => 'grand_total',
        'invoice_vendor' => 'No.InvoiceVendor',
        'nomor_bank' => 'No.Bank',
        'group' => 'Group'
    ];

    // Cek kolom yang tersedia
    $availableColumns = [];
    $missingColumns = [];

    foreach ($columnMapping as $key => $csvColumn) {
        if ($key === 'vendor') continue; // Skip vendor karena fixed value

        if (isset($headerMap[$csvColumn])) {
            $availableColumns[$key] = $csvColumn;
        } else {
            $missingColumns[] = "$key ($csvColumn)";
        }
    }

    echo "✅ Kolom yang tersedia:\n";
    foreach ($availableColumns as $key => $csvCol) {
        echo "   $key -> $csvCol\n";
    }

    if (!empty($missingColumns)) {
        echo "\n⚠️ Kolom yang tidak ditemukan: " . implode(', ', $missingColumns) . "\n";
    }
    echo "\n";

    $dataRows = [];
    $rowNumber = 1;
    $totalAmount = 0;
    $errors = [];

    // Baca semua data
    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        $rowNumber++;

        if (empty(array_filter($row))) continue; // Skip empty rows

        try {
            // Ambil data sesuai mapping kolom CSV Zona
            $nomorKontainer = isset($headerMap['Kontainer']) ? trim($row[$headerMap['Kontainer']] ?? '') : '';
            $vendor = 'ZONA'; // Fixed value
            $size = isset($headerMap['Ukuran']) ? trim($row[$headerMap['Ukuran']] ?? '') : '';
            $periode = isset($headerMap['Periode']) ? trim($row[$headerMap['Periode']] ?? '') : '';
            $tanggalAwal = isset($headerMap['Awal']) ? trim($row[$headerMap['Awal']] ?? '') : '';
            $tanggalAkhir = isset($headerMap['Akhir']) ? trim($row[$headerMap['Akhir']] ?? '') : '';

            // Parse numeric values - handle both comma and dot as decimal separator
            $dppRaw = isset($headerMap['DPP']) ? trim($row[$headerMap['DPP']] ?? '0') : '0';
            $ppnRaw = isset($headerMap['ppn']) ? trim($row[$headerMap['ppn']] ?? '0') : '0';
            $pphRaw = isset($headerMap['pph']) ? trim($row[$headerMap['pph']] ?? '0') : '0';
            $grandTotalRaw = isset($headerMap['grand_total']) ? trim($row[$headerMap['grand_total']] ?? '0') : '0';

            // Convert to float
            $dpp = (float) str_replace(',', '.', str_replace('.', '', $dppRaw));
            $ppn = (float) str_replace(',', '.', str_replace('.', '', $ppnRaw));
            $pph = (float) str_replace(',', '.', str_replace('.', '', $pphRaw));
            $grandTotal = (float) str_replace(',', '.', str_replace('.', '', $grandTotalRaw));

            // Ambil data invoice vendor dan nomor bank
            $invoiceVendor = isset($headerMap['No.InvoiceVendor']) ? trim($row[$headerMap['No.InvoiceVendor']] ?? '') : '';
            $nomorBank = isset($headerMap['No.Bank']) ? trim($row[$headerMap['No.Bank']] ?? '') : '';
            $group = isset($headerMap['Group']) ? trim($row[$headerMap['Group']] ?? '1') : '1';

            // Debug untuk baris pertama
            if ($rowNumber == 2) {
                echo "📋 Debug baris pertama data:\n";
                echo "   Invoice Vendor: '{$invoiceVendor}'\n";
                echo "   Nomor Bank: '{$nomorBank}'\n";
                echo "   Group: '{$group}'\n\n";
            }

            // Validasi data wajib
            if (empty($nomorKontainer) || empty($vendor) || empty($periode)) {
                $errors[] = "Baris $rowNumber: Data tidak lengkap (nomor kontainer, vendor, atau periode kosong)";
                continue;
            }

            // Parse tanggal dengan berbagai format
            $tanggalAwalParsed = null;
            $tanggalAkhirParsed = null;

            if (!empty($tanggalAwal)) {
                // Coba format: "07 Jun 23", "d M y", "d-m-Y", "Y-m-d"
                $dateFormats = ['d M y', 'd-m-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y'];
                $parsed = false;

                foreach ($dateFormats as $format) {
                    try {
                        $tanggalAwalParsed = Carbon::createFromFormat($format, $tanggalAwal)->format('Y-m-d');
                        $parsed = true;
                        break;
                    } catch (Exception $e) {
                        continue;
                    }
                }

                if (!$parsed) {
                    // Skip validasi tanggal untuk sekarang, biarkan NULL
                    $tanggalAwalParsed = null;
                }
            }

            if (!empty($tanggalAkhir)) {
                // Coba format: "07 Jun 23", "d M y", "d-m-Y", "Y-m-d"
                $dateFormats = ['d M y', 'd-m-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y'];
                $parsed = false;

                foreach ($dateFormats as $format) {
                    try {
                        $tanggalAkhirParsed = Carbon::createFromFormat($format, $tanggalAkhir)->format('Y-m-d');
                        $parsed = true;
                        break;
                    } catch (Exception $e) {
                        continue;
                    }
                }

                if (!$parsed) {
                    // Skip validasi tanggal untuk sekarang, biarkan NULL
                    $tanggalAkhirParsed = null;
                }
            }

            // Simpan data untuk insert ke database
            $dataRows[] = [
                'nomor_kontainer' => $nomorKontainer,
                'vendor' => $vendor,
                'size' => $size,
                'periode' => (int)$periode,
                'tanggal_awal' => $tanggalAwalParsed,
                'tanggal_akhir' => $tanggalAkhirParsed,
                'dpp' => $dpp,
                'ppn' => $ppn,
                'pph' => $pph,
                'grand_total' => $grandTotal,
                'status_pranota' => null,
                'pranota_id' => null,
                'group' => $group,
                'masa' => '1 Bulan',
                'tarif' => 'Bulanan',
                'tarif_nominal' => $dpp,
                'adjustment' => 0.00,
                'dpp_nilai_lain' => round($dpp * 11 / 12, 2),
                'status' => 'active',
                // Simpan info untuk filtering (tidak masuk database)
                'temp_invoice_vendor' => $invoiceVendor,
                'temp_nomor_bank' => $nomorBank,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $totalAmount += $grandTotal;

        } catch (Exception $e) {
            $errors[] = "Baris $rowNumber: Error parsing - " . $e->getMessage();
        }
    }

    fclose($handle);

    if (empty($dataRows)) {
        echo "❌ Tidak ada data valid yang bisa diproses\n";
        if (!empty($errors)) {
            echo "\n⚠️ Errors:\n";
            foreach ($errors as $error) {
                echo "   - $error\n";
            }
        }
        exit(1);
    }

    echo "✅ Data berhasil dibaca: " . count($dataRows) . " records\n";
    echo "💰 Total amount: Rp " . number_format($totalAmount, 2, ',', '.') . "\n\n";

    // Insert data ke tabel daftar_tagihan_kontainer_sewa
    echo "🔄 Memasukkan data ke tabel daftar_tagihan_kontainer_sewa...\n";

    $insertedIds = [];
    foreach ($dataRows as $data) {
        // Hapus kolom temp sebelum insert
        unset($data['temp_invoice_vendor']);
        unset($data['temp_nomor_bank']);

        $id = DB::table('daftar_tagihan_kontainer_sewa')->insertGetId($data);
        $insertedIds[] = $id;
    }

    echo "✅ Berhasil insert " . count($insertedIds) . " tagihan\n\n";

    // Generate nomor pranota dengan format PMS
    echo "🔄 Membuat pranota...\n";

    $nomorCetakan = 1;
    $tahun = Carbon::now()->format('y');
    $bulan = Carbon::now()->format('m');

    // Get next nomor pranota from master nomor terakhir dengan modul PMS
    $nomorTerakhir = NomorTerakhir::where('modul', 'PMS')->lockForUpdate()->first();
    if (!$nomorTerakhir) {
        // Create if not exists
        $nomorTerakhir = NomorTerakhir::create([
            'modul' => 'PMS',
            'nomor_terakhir' => 0,
            'keterangan' => 'Pranota kontainer sewa'
        ]);
    }

    $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
    $noInvoice = "PMS{$nomorCetakan}{$bulan}{$tahun}" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    $nomorTerakhir->nomor_terakhir = $nextNumber;
    $nomorTerakhir->save();

    // Cari data yang punya KEDUA nomor invoice vendor DAN nomor bank
    $kondisiKhusus = [];
    $selectedIds = [];
    $skippedData = [];

    echo "🔍 Mencari data dengan nomor invoice vendor DAN nomor bank...\n";

    for ($i = 0; $i < count($dataRows); $i++) {
        $data = $dataRows[$i];
        $invoiceVendor = trim($data['temp_invoice_vendor'] ?? '');
        $nomorBank = trim($data['temp_nomor_bank'] ?? '');

        // Debugging: cek isi kolom No.Bank
        $csvRow = $i + 2; // +2 karena header dan 0-based index
        if ($csvRow <= 5) { // Tampilkan 5 baris pertama untuk debug
            echo "Debug Baris $csvRow: Invoice='{$invoiceVendor}', Bank='{$nomorBank}'\n";
        }

        if (!empty($invoiceVendor) && !empty($nomorBank)) {
            $kondisiKhusus[] = $data;
            $selectedIds[] = $insertedIds[$i];
        } else {
            $skippedData[] = [
                'kontainer' => $data['nomor_kontainer'],
                'invoice_vendor' => $invoiceVendor,
                'nomor_bank' => $nomorBank,
                'reason' => empty($invoiceVendor) ? 'Tidak ada nomor invoice vendor' : 'Tidak ada nomor bank'
            ];
        }
    }

    echo "\n📊 HASIL FILTER:\n";
    echo "✅ Data yang memenuhi kondisi (punya invoice vendor DAN nomor bank): " . count($kondisiKhusus) . "\n";
    echo "❌ Data yang tidak memenuhi kondisi: " . count($skippedData) . "\n\n";

    if (count($skippedData) > 0) {
        echo "📋 Contoh data yang di-skip (5 pertama):\n";
        for ($i = 0; $i < min(5, count($skippedData)); $i++) {
            $skip = $skippedData[$i];
            echo "   - {$skip['kontainer']}: {$skip['reason']}\n";
        }
        echo "\n";
    }

    if (empty($selectedIds)) {
        echo "⚠️ Tidak ada data yang memenuhi kondisi untuk dibuat pranota\n";
        echo "💡 Semua data tetap tersimpan di database, tapi tidak masuk pranota\n";
        DB::commit();
        exit(0);
    }

    echo "📋 Membuat pranota untuk " . count($selectedIds) . " kontainer yang memenuhi kondisi\n";

    // Hitung total amount untuk pranota
    $pranotaTotalAmount = DaftarTagihanKontainerSewa::whereIn('id', $selectedIds)->sum('grand_total');

    // Create pranota
    $pranota = PranotaTagihanKontainerSewa::create([
        'no_invoice' => $noInvoice,
        'total_amount' => $pranotaTotalAmount,
        'keterangan' => 'Import CSV Zona - ' . count($selectedIds) . ' kontainer dengan invoice vendor dan nomor bank',
        'status' => 'unpaid',
        'tagihan_kontainer_sewa_ids' => $selectedIds,
        'jumlah_tagihan' => count($selectedIds),
        'tanggal_pranota' => Carbon::now()->format('Y-m-d'),
        'due_date' => Carbon::now()->addDays(30)->format('Y-m-d')
    ]);

    // Update tagihan yang masuk pranota
    DaftarTagihanKontainerSewa::whereIn('id', $selectedIds)->update([
        'status_pranota' => 'included',
        'pranota_id' => $pranota->id
    ]);

    DB::commit();

    echo "🎉 BERHASIL!\n";
    echo "==================\n";
    echo "📋 Pranota: {$pranota->no_invoice}\n";
    echo "💰 Total Amount: Rp " . number_format((float)$pranota->total_amount, 2, ',', '.') . "\n";
    echo "📦 Jumlah Kontainer: " . count($selectedIds) . "\n";
    echo "📊 Total Data Diimport: " . count($dataRows) . "\n\n";

    if (!empty($errors)) {
        echo "⚠️ Errors yang terjadi:\n";
        foreach ($errors as $error) {
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
