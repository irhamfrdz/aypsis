<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use App\Models\NomorTerakhir;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== ANALISIS & IMPORT ZONA CSV DENGAN GROUPING PRANOTA ===\n\n";

$csvFile = 'C:\\Users\\amanda\\Downloads\\Zona.csv';

if (!file_exists($csvFile)) {
    echo "❌ File tidak ditemukan: $csvFile\n";
    exit(1);
}

try {
    // Analisis CSV structure dulu
    $handle = fopen($csvFile, 'r');

    // Skip BOM if exists
    $first_line = fgets($handle);
    if (substr($first_line, 0, 3) == "\xEF\xBB\xBF") {
        $first_line = substr($first_line, 3);
    }
    rewind($handle);

    // Detect delimiter
    $line = fgets($handle);
    rewind($handle);

    $delimiter = ';';
    if (substr_count($line, ',') > substr_count($line, ';')) {
        $delimiter = ',';
    }

    echo "🔍 Delimiter detected: '$delimiter'\n";

    // Get header
    $header = fgetcsv($handle, 0, $delimiter);

    // Clean header
    $header = array_map(function($h) {
        return trim(str_replace(["\xEF\xBB\xBF", "\x00"], '', $h));
    }, $header);

    echo "📋 Header CSV:\n";
    foreach ($header as $i => $col) {
        echo "   $i: '$col'\n";
    }
    echo "\n";

    // Map kolom yang diperlukan
    $headerMap = array_flip($header);

    // Cari kolom invoice dan bank dengan lebih spesifik
    $invoiceColumn = null;
    $bankColumn = null;

    foreach ($header as $i => $col) {
        $colLower = strtolower($col);
        // Cari kolom No.InvoiceVendor
        if (strpos($colLower, 'no.invoicevendor') !== false || $col === 'No.InvoiceVendor') {
            $invoiceColumn = $i;
        }
        // Cari kolom No.Bank
        if (strpos($colLower, 'no.bank') !== false || $col === 'No.Bank') {
            $bankColumn = $i;
        }
    }

    echo "🔎 Invoice column: " . ($invoiceColumn !== null ? "$invoiceColumn ({$header[$invoiceColumn]})" : "NOT FOUND") . "\n";
    echo "🔎 Bank column: " . ($bankColumn !== null ? "$bankColumn ({$header[$bankColumn]})" : "NOT FOUND") . "\n\n";

    if ($invoiceColumn === null || $bankColumn === null) {
        echo "❌ Kolom invoice atau bank tidak ditemukan. Mari lihat sample data:\n\n";

        // Show sample rows
        $sampleCount = 0;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false && $sampleCount < 5) {
            echo "Sample Row " . ($sampleCount + 1) . ":\n";
            foreach ($row as $i => $value) {
                $colName = $header[$i] ?? "Column_$i";
                echo "   $i ($colName): '" . trim($value) . "'\n";
            }
            echo "\n";
            $sampleCount++;
        }

        fclose($handle);
        exit(1);
    }

    // Reset file pointer
    rewind($handle);
    fgetcsv($handle, 0, $delimiter); // skip header

    $dataRows = [];
    $rowNumber = 1;
    $stats = [
        'total_rows' => 0,
        'with_invoice_and_bank' => 0,
        'with_invoice_only' => 0,
        'with_bank_only' => 0,
        'empty_both' => 0
    ];

    // Baca semua data dan kelompokkan
    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
        $rowNumber++;

        if (empty(array_filter($row))) continue; // Skip empty rows

        $stats['total_rows']++;

        // Extract data
        $nomorKontainer = trim($row[0] ?? '');
        $invoiceVendor = trim($row[$invoiceColumn] ?? '');
        $nomorBank = trim($row[$bankColumn] ?? '');

        // Statistik
        $hasInvoice = !empty($invoiceVendor);
        $hasBank = !empty($nomorBank);

        if ($hasInvoice && $hasBank) {
            $stats['with_invoice_and_bank']++;
        } elseif ($hasInvoice && !$hasBank) {
            $stats['with_invoice_only']++;
        } elseif (!$hasInvoice && $hasBank) {
            $stats['with_bank_only']++;
        } else {
            $stats['empty_both']++;
        }

        // Hanya simpan yang memiliki invoice DAN bank
        if ($hasInvoice && $hasBank) {
            // Buat key untuk grouping: kombinasi invoice + bank
            $groupKey = $invoiceVendor . '|' . $nomorBank;

            if (!isset($dataRows[$groupKey])) {
                $dataRows[$groupKey] = [
                    'invoice_vendor' => $invoiceVendor,
                    'nomor_bank' => $nomorBank,
                    'kontainers' => []
                ];
            }

            $dataRows[$groupKey]['kontainers'][] = [
                'nomor_kontainer' => $nomorKontainer,
                'row_data' => $row,
                'row_number' => $rowNumber
            ];
        }
    }

    fclose($handle);

    echo "📊 STATISTIK DATA:\n";
    echo "==================\n";
    echo "📋 Total rows: {$stats['total_rows']}\n";
    echo "✅ Invoice + Bank: {$stats['with_invoice_and_bank']}\n";
    echo "⚠️ Invoice only: {$stats['with_invoice_only']}\n";
    echo "⚠️ Bank only: {$stats['with_bank_only']}\n";
    echo "❌ Both empty: {$stats['empty_both']}\n\n";

    echo "🎯 GROUPING UNTUK PRANOTA:\n";
    echo "==========================\n";
    echo "📦 Total groups (pranota): " . count($dataRows) . "\n\n";

    $totalKontainers = 0;
    foreach ($dataRows as $groupKey => $group) {
        $kontainerCount = count($group['kontainers']);
        $totalKontainers += $kontainerCount;
        echo "Group: {$group['invoice_vendor']} + {$group['nomor_bank']} = $kontainerCount kontainer\n";

        // Show first 3 kontainers
        $showCount = min(3, $kontainerCount);
        for ($i = 0; $i < $showCount; $i++) {
            echo "   - {$group['kontainers'][$i]['nomor_kontainer']}\n";
        }
        if ($kontainerCount > 3) {
            echo "   ... dan " . ($kontainerCount - 3) . " kontainer lainnya\n";
        }
        echo "\n";
    }

    echo "📊 Total kontainer yang akan diproses: $totalKontainers\n\n";

    // Auto confirm untuk otomasi
    echo "✅ Melanjutkan import dengan grouping otomatis...\n";

    echo "\n🔄 Memulai proses import...\n\n";

    DB::beginTransaction();

    $processedGroups = 0;
    $processedKontainers = 0;
    $createdPranota = [];
    $errors = [];

    foreach ($dataRows as $groupKey => $group) {
        try {
            $invoiceVendor = $group['invoice_vendor'];
            $nomorBank = $group['nomor_bank'];
            $kontainers = $group['kontainers'];

            echo "🔄 Processing group: $invoiceVendor + $nomorBank (" . count($kontainers) . " kontainer)\n";

            $tagihan_ids = [];
            $total_dpp = 0;
            $total_ppn = 0;
            $total_pph = 0;
            $total_grand_total = 0;

            // Process each kontainer in the group
            foreach ($kontainers as $kontainerData) {
                $row = $kontainerData['row_data'];
                $nomorKontainer = $kontainerData['nomor_kontainer'];

                // Extract other fields from CSV berdasarkan header yang sudah diidentifikasi
                $vendor = 'ZONA'; // Default vendor
                $size = trim($row[4] ?? ''); // Ukuran
                $periode = trim($row[6] ?? date('Ym')); // Periode
                $group = trim($row[0] ?? ''); // Group
                $tanggalAwal = trim($row[2] ?? ''); // Awal
                $tanggalAkhir = trim($row[3] ?? ''); // Akhir

                // Parse financial data berdasarkan kolom yang sudah diidentifikasi
                $dppRaw = trim($row[9] ?? '0'); // DPP
                $ppnRaw = trim($row[14] ?? '0'); // ppn
                $pphRaw = trim($row[15] ?? '0'); // pph
                $grandTotalRaw = trim($row[16] ?? '0'); // grand_total
                $adjustmentRaw = trim($row[12] ?? '0'); // adjustment

                // Convert to numbers
                $dpp = (float) str_replace([',', '.'], ['', '.'], preg_replace('/[^0-9,.-]/', '', $dppRaw));
                $ppn = (float) str_replace([',', '.'], ['', '.'], preg_replace('/[^0-9,.-]/', '', $ppnRaw));
                $pph = (float) str_replace([',', '.'], ['', '.'], preg_replace('/[^0-9,.-]/', '', $pphRaw));
                $grandTotal = (float) str_replace([',', '.'], ['', '.'], preg_replace('/[^0-9,.-]/', '', $grandTotalRaw));
                $adjustment = (float) str_replace([',', '.'], ['', '.'], preg_replace('/[^0-9,.-]/', '', $adjustmentRaw));

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

                // Insert to database
                $tagihan = DaftarTagihanKontainerSewa::create([
                    'nomor_kontainer' => $nomorKontainer,
                    'vendor' => $vendor,
                    'size' => $size,
                    'periode' => is_numeric($periode) ? (int)$periode : date('Ym'),
                    'tanggal_awal' => $tanggalAwalParsed,
                    'tanggal_akhir' => $tanggalAkhirParsed,
                    'group' => $group,
                    'nomor_invoice_vendor' => $invoiceVendor,
                    'nomor_bank' => $nomorBank,
                    'dpp' => $dpp,
                    'adjustment' => $adjustment,
                    'ppn' => $ppn,
                    'pph' => $pph,
                    'grand_total' => $grandTotal,
                    'status' => 'active'
                ]);

                $tagihan_ids[] = $tagihan->id;
                $total_dpp += $dpp;
                $total_ppn += $ppn;
                $total_pph += $pph;
                $total_grand_total += $grandTotal;

                $processedKontainers++;
            }

            // Create pranota for this group
            $nomorTerakhir = NomorTerakhir::where('jenis', 'pranota_tagihan_kontainer_sewa')->first();
            if (!$nomorTerakhir) {
                $nomorTerakhir = NomorTerakhir::create([
                    'jenis' => 'pranota_tagihan_kontainer_sewa',
                    'nomor_terakhir' => 0
                ]);
            }

            $nomorTerakhir->increment('nomor_terakhir');
            $nomorPranota = "PMS11025" . str_pad($nomorTerakhir->nomor_terakhir, 6, '0', STR_PAD_LEFT);

            $pranota = PranotaTagihanKontainerSewa::create([
                'nomor_pranota' => $nomorPranota,
                'tagihan_ids' => implode(',', $tagihan_ids),
                'total_dpp' => $total_dpp,
                'total_ppn' => $total_ppn,
                'total_pph' => $total_pph,
                'total_grand_total' => $total_grand_total,
                'status' => 'active'
            ]);

            $createdPranota[] = [
                'nomor_pranota' => $nomorPranota,
                'invoice_vendor' => $invoiceVendor,
                'nomor_bank' => $nomorBank,
                'kontainer_count' => count($kontainers),
                'total_amount' => $total_grand_total
            ];

            echo "   ✅ Pranota $nomorPranota dibuat (Rp " . number_format($total_grand_total, 0, ',', '.') . ")\n";

            $processedGroups++;

        } catch (Exception $e) {
            $errors[] = "Error processing group $groupKey: " . $e->getMessage();
            echo "   ❌ Error: " . $e->getMessage() . "\n";
        }
    }

    DB::commit();

    echo "\n🎉 IMPORT SELESAI!\n";
    echo "==================\n";
    echo "✅ Groups diproses: $processedGroups\n";
    echo "✅ Kontainer diproses: $processedKontainers\n";
    echo "✅ Pranota dibuat: " . count($createdPranota) . "\n";
    echo "❌ Errors: " . count($errors) . "\n\n";

    if (!empty($createdPranota)) {
        echo "📋 PRANOTA YANG DIBUAT:\n";
        echo "=======================\n";
        foreach ($createdPranota as $pranota) {
            echo "• {$pranota['nomor_pranota']}: {$pranota['invoice_vendor']} + {$pranota['nomor_bank']}\n";
            echo "  {$pranota['kontainer_count']} kontainer, Rp " . number_format($pranota['total_amount'], 0, ',', '.') . "\n\n";
        }
    }

    if (!empty($errors)) {
        echo "⚠️ ERRORS:\n";
        echo "==========\n";
        foreach ($errors as $error) {
            echo "• $error\n";
        }
    }

} catch (Exception $e) {
    if (DB::transactionLevel() > 0) {
        DB::rollback();
    }
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n✅ Selesai!\n";
