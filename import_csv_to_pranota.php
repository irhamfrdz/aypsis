<?php
/**
 * Script untuk import data CSV ke pranota
 * - Group by nomor invoice vendor yang sama
 * - Hanya buat pranota jika ada nomor bank
 * - Tanggal pranota menggunakan tanggal bank
 */

echo "=== IMPORT CSV TO PRANOTA ===\n\n";

// Include Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$csvFile = 'Zona.csv';

if (!file_exists($csvFile)) {
    echo "❌ File tidak ditemukan: $csvFile\n";
    exit(1);
}

echo "🔍 Membaca file CSV...\n";

// Baca CSV file
$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "❌ Tidak dapat membuka file CSV\n";
    exit(1);
}

// Read header
$headers = fgetcsv($handle, 1000, ';');
echo "📋 Headers: " . implode(' | ', $headers) . "\n\n";

// Group data by invoice vendor
$invoiceGroups = [];
$rowCount = 0;
$processedCount = 0;
$skipCount = 0;

echo "📊 Memproses data CSV...\n";

while (($row = fgetcsv($handle, 1000, ';')) !== false) {
    $rowCount++;

    // Skip empty rows or invalid data
    if (count($row) < 20) {
        continue;
    }

    // Extract data from CSV
    $data = [
        'group' => trim($row[0]),
        'kontainer' => trim($row[1]),
        'awal' => trim($row[2]),
        'akhir' => trim($row[3]),
        'ukuran' => trim($row[4]),
        'harga' => trim($row[5]),
        'periode' => trim($row[6]),
        'status' => trim($row[7]),
        'hari' => trim($row[8]),
        'dpp' => trim($row[9]),
        'keterangan' => trim($row[10]),
        'qty_disc' => trim($row[11]),
        'adjustment' => trim($row[12]),
        'pembulatan' => trim($row[13]),
        'ppn' => trim($row[14]),
        'pph' => trim($row[15]),
        'grand_total' => trim($row[16]),
        'no_invoice_vendor' => trim($row[17]),
        'tgl_inv_vendor' => trim($row[18]),
        'no_bank' => trim($row[19]),
        'tgl_bank' => trim($row[20]),
    ];

    // Skip jika tidak ada nomor invoice vendor atau nomor bank kosong
    if (empty($data['no_invoice_vendor']) || empty($data['no_bank']) || $data['no_bank'] === '-' || empty($data['tgl_bank'])) {
        $skipCount++;
        continue;
    }

    // Group by invoice vendor number
    $invoiceKey = $data['no_invoice_vendor'];

    if (!isset($invoiceGroups[$invoiceKey])) {
        $invoiceGroups[$invoiceKey] = [
            'items' => [],
            'invoice_info' => [
                'no_invoice_vendor' => $data['no_invoice_vendor'],
                'tgl_inv_vendor' => $data['tgl_inv_vendor'],
                'no_bank' => $data['no_bank'],
                'tgl_bank' => $data['tgl_bank'],
            ]
        ];
    }

    $invoiceGroups[$invoiceKey]['items'][] = $data;
    $processedCount++;
}

fclose($handle);

echo "✅ Selesai membaca CSV\n";
echo "📊 Total rows: $rowCount\n";
echo "📊 Rows processed: $processedCount\n";
echo "📊 Rows skipped (no bank info): $skipCount\n";
echo "📊 Invoice groups: " . count($invoiceGroups) . "\n\n";

// Show preview of grouped data
echo "📋 Preview grouped data:\n";
echo str_repeat("-", 80) . "\n";
printf("%-20s %-12s %-12s %-15s %s\n", 'Invoice No', 'Bank No', 'Bank Date', 'Items Count', 'Total DPP');
echo str_repeat("-", 80) . "\n";

$totalDppPreview = 0;
foreach ($invoiceGroups as $invoiceNo => $group) {
    $itemCount = count($group['items']);
    $groupDpp = 0;

    foreach ($group['items'] as $item) {
        $dppValue = str_replace([' ', '.', ','], '', $item['dpp']);
        $groupDpp += (float)$dppValue;
    }

    $totalDppPreview += $groupDpp;

    printf("%-20s %-12s %-12s %-15s %s\n",
        substr($invoiceNo, 0, 19),
        $group['invoice_info']['no_bank'],
        $group['invoice_info']['tgl_bank'],
        $itemCount . ' items',
        'Rp ' . number_format($groupDpp, 0)
    );
}

echo str_repeat("-", 80) . "\n";
echo "Total DPP keseluruhan: Rp " . number_format($totalDppPreview, 0) . "\n\n";

// Confirm creation
echo "⚠️  Apakah Anda yakin ingin membuat " . count($invoiceGroups) . " pranota dari data ini?\n";
echo "Ketik 'YES CREATE PRANOTA' untuk melanjutkan atau Enter untuk batal: ";

$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if ($confirmation !== 'YES CREATE PRANOTA') {
    echo "\n❌ Operasi dibatalkan.\n";
    exit(0);
}

echo "\n🔄 Membuat pranota...\n";

DB::beginTransaction();

try {
    $createdPranota = 0;
    $createdTagihan = 0;
    $errors = [];

    foreach ($invoiceGroups as $invoiceNo => $group) {
        try {
            // Parse tanggal bank
            $tglBank = $group['invoice_info']['tgl_bank'];
            $pranotaDate = null;

            // Try different date formats
            try {
                if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{2})/', $tglBank, $matches)) {
                    $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                    $monthMap = [
                        'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                        'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                        'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
                    ];
                    $month = $monthMap[$matches[2]] ?? '01';
                    $year = '20' . $matches[3];
                    $pranotaDate = "$year-$month-$day";
                }
            } catch (Exception $e) {
                // If date parsing fails, use current date
                $pranotaDate = date('Y-m-d');
            }

            // Calculate total for this group
            $totalDpp = 0;
            $totalAdjustment = 0;
            $totalPpn = 0;
            $totalPph = 0;
            $totalGrandTotal = 0;

            foreach ($group['items'] as $item) {
                $dpp = (float)str_replace([' ', '.', ','], '', $item['dpp']);
                $adjustment = (float)str_replace([' ', '.', ',', '-'], '', $item['adjustment']);
                if ($item['adjustment'] !== '-' && strpos($item['adjustment'], '-') !== false) {
                    $adjustment = -$adjustment;
                }
                $ppn = (float)str_replace([' ', '.', ','], '', $item['ppn']);
                $pph = (float)str_replace([' ', '.', ','], '', $item['pph']);
                $grandTotal = (float)str_replace([' ', '.', ','], '', $item['grand_total']);

                $totalDpp += $dpp;
                $totalAdjustment += $adjustment;
                $totalPpn += $ppn;
                $totalPph += $pph;
                $totalGrandTotal += $grandTotal;
            }

            // Create pranota
            $pranota = PranotaTagihanKontainerSewa::create([
                'no_invoice' => 'PRN-ZONA-' . date('Ymd') . '-' . str_pad($createdPranota + 1, 4, '0', STR_PAD_LEFT),
                'tanggal_pranota' => $pranotaDate,
                'no_invoice_vendor' => $invoiceNo,
                'tgl_invoice_vendor' => $group['invoice_info']['tgl_inv_vendor'],
                'total_amount' => $totalGrandTotal,
                'status' => 'unpaid',
                'keterangan' => 'Import dari CSV - Invoice: ' . $invoiceNo . ' | Bank: ' . $group['invoice_info']['no_bank'],
                'jumlah_tagihan' => count($group['items']),
                'due_date' => $pranotaDate,
            ]);

            $createdPranota++;

            // Create tagihan items for this pranota
            $tagihanIds = [];

            foreach ($group['items'] as $item) {
                // Convert dates
                $tanggalAwal = null;
                $tanggalAkhir = null;

                try {
                    if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{2})/', $item['awal'], $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $monthMap = [
                            'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                            'May' => '05', 'Jun' => '06', 'Jul' => '07', 'Aug' => '08',
                            'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
                        ];
                        $month = $monthMap[$matches[2]] ?? '01';
                        $year = '20' . $matches[3];
                        $tanggalAwal = "$year-$month-$day";
                    }

                    if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{2})/', $item['akhir'], $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $month = $monthMap[$matches[2]] ?? '01';
                        $year = '20' . $matches[3];
                        $tanggalAkhir = "$year-$month-$day";
                    }
                } catch (Exception $e) {
                    // Use default dates if parsing fails
                    $tanggalAwal = date('Y-m-d');
                    $tanggalAkhir = date('Y-m-d');
                }

                // Calculate financial values
                $dpp = (float)str_replace([' ', '.', ','], '', $item['dpp']);
                $adjustment = 0;
                if ($item['adjustment'] !== '-' && !empty($item['adjustment'])) {
                    $adjustment = (float)str_replace([' ', '.', ',', '-'], '', $item['adjustment']);
                    if (strpos($item['adjustment'], '-') !== false) {
                        $adjustment = -$adjustment;
                    }
                }
                $ppn = (float)str_replace([' ', '.', ','], '', $item['ppn']);
                $pph = (float)str_replace([' ', '.', ','], '', $item['pph']);
                $grandTotal = (float)str_replace([' ', '.', ','], '', $item['grand_total']);

                $tagihan = DaftarTagihanKontainerSewa::create([
                    'vendor' => 'ZONA',
                    'nomor_kontainer' => $item['kontainer'],
                    'size' => $item['ukuran'],
                    'tanggal_awal' => $tanggalAwal,
                    'tanggal_akhir' => $tanggalAkhir,
                    'tarif' => ucfirst(strtolower($item['status'])), // Bulanan/Harian
                    'periode' => (int)$item['periode'],
                    'group' => $item['group'],
                    'status' => 'ongoing',
                    'masa' => $item['hari'] . ' hari',
                    'dpp' => $dpp,
                    'adjustment' => $adjustment,
                    'ppn' => $ppn,
                    'pph' => $pph,
                    'grand_total' => $grandTotal,
                    'status_pranota' => 'included',
                    'pranota_id' => $pranota->id,
                ]);

                $tagihanIds[] = $tagihan->id;
                $createdTagihan++;
            }

            // Update pranota with tagihan IDs
            $pranota->update([
                'tagihan_kontainer_sewa_ids' => $tagihanIds
            ]);

            echo "✅ Pranota #{$pranota->id} - {$pranota->no_invoice} - " . count($group['items']) . " items - Rp " . number_format($totalGrandTotal, 0) . "\n";

        } catch (Exception $e) {
            $errors[] = "Error creating pranota for invoice $invoiceNo: " . $e->getMessage();
            echo "❌ Error for invoice $invoiceNo: " . $e->getMessage() . "\n";
        }
    }

    DB::commit();

    echo "\n🎉 Import selesai!\n";
    echo "📊 Pranota created: $createdPranota\n";
    echo "📊 Tagihan created: $createdTagihan\n";

    if (!empty($errors)) {
        echo "\n⚠️  Errors encountered:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }

} catch (Exception $e) {
    DB::rollback();
    echo "\n❌ Import gagal: " . $e->getMessage() . "\n";
    echo "🔄 Database rollback completed\n";
    exit(1);
}

echo "\n=== IMPORT COMPLETED ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
