<?php

/**
 * Script untuk menganalisis data CSV Zona dan menguji logika grouping
 * kontainer berdasarkan nomor invoice vendor dan nomor bank yang sama
 */

// Path ke file CSV
$csvFile = 'c:\Users\amanda\Downloads\Zona.csv';

if (!file_exists($csvFile)) {
    die("File CSV tidak ditemukan: {$csvFile}\n");
}

echo "=== ANALISIS DATA ZONA CSV ===\n";
echo "File: {$csvFile}\n\n";

// Read CSV data
$csvData = [];
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    $header = fgetcsv($handle, 1000, ";"); // Read header

    echo "Header CSV:\n";
    foreach ($header as $index => $col) {
        echo "{$index}: {$col}\n";
    }
    echo "\n";

    // Find relevant column indices
    $kontainerIndex = array_search('Kontainer', $header);
    $invoiceVendorIndex = array_search('No.InvoiceVendor', $header);
    $bankIndex = array_search('No.Bank', $header);
    $tglInvoiceIndex = array_search('Tgl.InvVendor', $header);
    $tglBankIndex = array_search('Tgl.Bank', $header);
    $grandTotalIndex = array_search(' grand_total ', $header);
    $groupIndex = array_search('Group', $header);

    echo "Indeks kolom yang relevan:\n";
    echo "Kontainer: {$kontainerIndex}\n";
    echo "No.InvoiceVendor: {$invoiceVendorIndex}\n";
    echo "No.Bank: {$bankIndex}\n";
    echo "Tgl.InvVendor: {$tglInvoiceIndex}\n";
    echo "Tgl.Bank: {$tglBankIndex}\n";
    echo "Grand Total: {$grandTotalIndex}\n";
    echo "Group: {$groupIndex}\n\n";

    // Read data rows
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Skip empty rows or header repetition
        if (empty($data[$kontainerIndex]) || $data[$kontainerIndex] == 'Kontainer') {
            continue;
        }

        $csvData[] = [
            'group' => $data[$groupIndex] ?? '',
            'kontainer' => $data[$kontainerIndex] ?? '',
            'no_invoice_vendor' => $data[$invoiceVendorIndex] ?? '',
            'tgl_invoice_vendor' => $data[$tglInvoiceIndex] ?? '',
            'no_bank' => $data[$bankIndex] ?? '',
            'tgl_bank' => $data[$tglBankIndex] ?? '',
            'grand_total' => isset($data[$grandTotalIndex]) ? floatval(str_replace([' ', '.', ','], ['', '', '.'], $data[$grandTotalIndex])) : 0,
            'raw_data' => $data
        ];
    }

    fclose($handle);
}

echo "Total data kontainer: " . count($csvData) . "\n\n";

// Analisis grouping berdasarkan nomor invoice vendor dan nomor bank
echo "=== ANALISIS GROUPING BERDASARKAN INVOICE VENDOR + NO BANK ===\n\n";

$groupedData = [];
$kontainerTanpaInvoice = [];
$kontainerTanpaBank = [];
$kontainerLengkap = [];

foreach ($csvData as $row) {
    $invoiceVendor = trim($row['no_invoice_vendor']);
    $noBank = trim($row['no_bank']);

    // Cek kelengkapan data
    if (empty($invoiceVendor)) {
        $kontainerTanpaInvoice[] = $row;
        continue;
    }

    if (empty($noBank)) {
        $kontainerTanpaBank[] = $row;
        continue;
    }

    // Data lengkap, masukkan ke grouping
    $kontainerLengkap[] = $row;
    $groupKey = $invoiceVendor . '|' . $noBank;

    if (!isset($groupedData[$groupKey])) {
        $groupedData[$groupKey] = [
            'no_invoice_vendor' => $invoiceVendor,
            'tgl_invoice_vendor' => $row['tgl_invoice_vendor'],
            'no_bank' => $noBank,
            'tgl_bank' => $row['tgl_bank'],
            'kontainer_list' => [],
            'total_amount' => 0,
            'count' => 0
        ];
    }

    $groupedData[$groupKey]['kontainer_list'][] = $row['kontainer'];
    $groupedData[$groupKey]['total_amount'] += $row['grand_total'];
    $groupedData[$groupKey]['count']++;
}

echo "STATISTIK DATA:\n";
echo "- Total kontainer: " . count($csvData) . "\n";
echo "- Kontainer dengan data lengkap (invoice + bank): " . count($kontainerLengkap) . "\n";
echo "- Kontainer tanpa invoice vendor: " . count($kontainerTanpaInvoice) . "\n";
echo "- Kontainer tanpa nomor bank: " . count($kontainerTanpaBank) . "\n";
echo "- Jumlah group yang akan terbentuk: " . count($groupedData) . "\n\n";

if (count($kontainerTanpaInvoice) > 0) {
    echo "KONTAINER TANPA INVOICE VENDOR (" . count($kontainerTanpaInvoice) . "):\n";
    foreach (array_slice($kontainerTanpaInvoice, 0, 10) as $row) {
        echo "- {$row['kontainer']} (Group: {$row['group']})\n";
    }
    if (count($kontainerTanpaInvoice) > 10) {
        echo "... dan " . (count($kontainerTanpaInvoice) - 10) . " lainnya\n";
    }
    echo "\n";
}

if (count($kontainerTanpaBank) > 0) {
    echo "KONTAINER TANPA NOMOR BANK (" . count($kontainerTanpaBank) . "):\n";
    foreach (array_slice($kontainerTanpaBank, 0, 10) as $row) {
        echo "- {$row['kontainer']} (Invoice: {$row['no_invoice_vendor']}, Group: {$row['group']})\n";
    }
    if (count($kontainerTanpaBank) > 10) {
        echo "... dan " . (count($kontainerTanpaBank) - 10) . " lainnya\n";
    }
    echo "\n";
}

echo "=== DETAIL GROUPING YANG AKAN TERBENTUK ===\n\n";

$groupCounter = 1;
foreach ($groupedData as $groupKey => $group) {
    echo "PRANOTA #{$groupCounter}:\n";
    echo "- Invoice Vendor: {$group['no_invoice_vendor']}\n";
    echo "- Tgl Invoice: {$group['tgl_invoice_vendor']}\n";
    echo "- Nomor Bank: {$group['no_bank']}\n";
    echo "- Tgl Bank: {$group['tgl_bank']}\n";
    echo "- Jumlah Kontainer: {$group['count']}\n";
    echo "- Total Amount: Rp " . number_format($group['total_amount'], 2, ',', '.') . "\n";
    echo "- Kontainer: " . implode(', ', array_slice($group['kontainer_list'], 0, 5));
    if (count($group['kontainer_list']) > 5) {
        echo " ... dan " . (count($group['kontainer_list']) - 5) . " lainnya";
    }
    echo "\n\n";

    $groupCounter++;

    // Limit display untuk readability
    if ($groupCounter > 10) {
        echo "... dan " . (count($groupedData) - 10) . " group lainnya\n\n";
        break;
    }
}

echo "=== RINGKASAN ANALISIS ===\n";
echo "Total kontainer dalam CSV: " . count($csvData) . "\n";
echo "Kontainer yang dapat diproses (memiliki invoice vendor + nomor bank): " . count($kontainerLengkap) . "\n";
echo "Jumlah pranota yang akan dibuat: " . count($groupedData) . "\n";
echo "Rata-rata kontainer per pranota: " . (count($groupedData) > 0 ? round(count($kontainerLengkap) / count($groupedData), 2) : 0) . "\n";
echo "Total nilai yang akan diproses: Rp " . number_format(array_sum(array_column($groupedData, 'total_amount')), 2, ',', '.') . "\n\n";

echo "REKOMENDASI:\n";
echo "1. " . count($kontainerLengkap) . " kontainer siap untuk dibuatkan " . count($groupedData) . " pranota\n";
if (count($kontainerTanpaInvoice) > 0) {
    echo "2. " . count($kontainerTanpaInvoice) . " kontainer perlu dilengkapi nomor invoice vendor\n";
}
if (count($kontainerTanpaBank) > 0) {
    echo "3. " . count($kontainerTanpaBank) . " kontainer perlu dilengkapi nomor bank\n";
}

// Generate sample SQL untuk testing
echo "\n=== SAMPLE SQL UNTUK TESTING ===\n";
echo "-- Contoh data untuk testing di database\n";
echo "-- Pastikan tabel daftar_tagihan_kontainer_sewa sudah ada\n\n";

$sampleCount = 0;
foreach ($groupedData as $groupKey => $group) {
    if ($sampleCount >= 2) break; // Hanya ambil 2 group untuk sample

    echo "-- Group " . ($sampleCount + 1) . ": Invoice {$group['no_invoice_vendor']}, Bank {$group['no_bank']}\n";

    foreach (array_slice($group['kontainer_list'], 0, 3) as $kontainer) {
        echo "INSERT INTO daftar_tagihan_kontainer_sewa (kontainer, no_invoice_vendor, no_bank, grand_total, status_pranota, supplier) VALUES\n";
        echo "('{$kontainer}', '{$group['no_invoice_vendor']}', '{$group['no_bank']}', " . ($group['total_amount'] / $group['count']) . ", 'pending', 'ZONA');\n";
    }
    echo "\n";

    $sampleCount++;
}

echo "=== SELESAI ANALISIS ===\n";
