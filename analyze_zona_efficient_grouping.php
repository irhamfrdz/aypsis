<?php

/**
 * Script untuk menganalisis grouping yang lebih baik -
 * fokus pada group yang memiliki beberapa kontainer dalam satu invoice+bank
 */

// Path ke file CSV
$csvFile = 'c:\Users\amanda\Downloads\Zona.csv';

if (!file_exists($csvFile)) {
    die("File CSV tidak ditemukan: {$csvFile}\n");
}

echo "=== ANALISIS GROUPING YANG EFISIEN ===\n\n";

// Read CSV data
$csvData = [];
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    $header = fgetcsv($handle, 1000, ";");

    // Find column indices
    $kontainerIndex = array_search('Kontainer', $header);
    $invoiceVendorIndex = array_search('No.InvoiceVendor', $header);
    $bankIndex = array_search('No.Bank', $header);
    $tglInvoiceIndex = array_search('Tgl.InvVendor', $header);
    $tglBankIndex = array_search('Tgl.Bank', $header);
    $grandTotalIndex = array_search(' grand_total ', $header);
    $groupIndex = array_search('Group', $header);

    // Read data rows
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        if (empty($data[$kontainerIndex]) || $data[$kontainerIndex] == 'Kontainer') {
            continue;
        }

        $csvData[] = [
            'group' => $data[$groupIndex] ?? '',
            'kontainer' => $data[$kontainerIndex] ?? '',
            'no_invoice_vendor' => trim($data[$invoiceVendorIndex] ?? ''),
            'tgl_invoice_vendor' => $data[$tglInvoiceIndex] ?? '',
            'no_bank' => trim($data[$bankIndex] ?? ''),
            'tgl_bank' => $data[$tglBankIndex] ?? '',
            'grand_total' => isset($data[$grandTotalIndex]) ? floatval(str_replace([' ', '.', ','], ['', '', '.'], $data[$grandTotalIndex])) : 0
        ];
    }

    fclose($handle);
}

// Group data
$groupedData = [];
$kontainerDenganDataLengkap = 0;

foreach ($csvData as $row) {
    if (!empty($row['no_invoice_vendor']) && !empty($row['no_bank'])) {
        $kontainerDenganDataLengkap++;
        $groupKey = $row['no_invoice_vendor'] . '|' . $row['no_bank'];

        if (!isset($groupedData[$groupKey])) {
            $groupedData[$groupKey] = [
                'no_invoice_vendor' => $row['no_invoice_vendor'],
                'tgl_invoice_vendor' => $row['tgl_invoice_vendor'],
                'no_bank' => $row['no_bank'],
                'tgl_bank' => $row['tgl_bank'],
                'kontainers' => [],
                'total_amount' => 0,
                'count' => 0,
                'groups' => []
            ];
        }

        $groupedData[$groupKey]['kontainers'][] = $row['kontainer'];
        $groupedData[$groupKey]['total_amount'] += $row['grand_total'];
        $groupedData[$groupKey]['count']++;

        // Track which groups are included
        if (!in_array($row['group'], $groupedData[$groupKey]['groups'])) {
            $groupedData[$groupKey]['groups'][] = $row['group'];
        }
    }
}

// Pisahkan group berdasarkan jumlah kontainer
$singleKontainer = [];
$multipleKontainer = [];

foreach ($groupedData as $groupKey => $group) {
    if ($group['count'] == 1) {
        $singleKontainer[$groupKey] = $group;
    } else {
        $multipleKontainer[$groupKey] = $group;
    }
}

// Sort berdasarkan jumlah kontainer (terbanyak dulu)
uasort($multipleKontainer, function($a, $b) {
    return $b['count'] - $a['count'];
});

echo "RINGKASAN HASIL GROUPING:\n";
echo "- Total kontainer dalam CSV: " . count($csvData) . "\n";
echo "- Kontainer dengan invoice vendor + nomor bank: {$kontainerDenganDataLengkap}\n";
echo "- Group dengan 1 kontainer: " . count($singleKontainer) . "\n";
echo "- Group dengan multiple kontainer: " . count($multipleKontainer) . "\n";
echo "- Total pranota yang akan dibuat: " . count($groupedData) . "\n\n";

if (!empty($multipleKontainer)) {
    echo "=== GROUP DENGAN MULTIPLE KONTAINER (EFISIEN) ===\n\n";

    $counter = 1;
    $totalKontainerMultiple = 0;

    foreach ($multipleKontainer as $groupKey => $group) {
        echo "PRANOTA #{$counter} (EFISIEN):\n";
        echo "- Invoice Vendor: {$group['no_invoice_vendor']}\n";
        echo "- Tgl Invoice: {$group['tgl_invoice_vendor']}\n";
        echo "- Nomor Bank: {$group['no_bank']}\n";
        echo "- Tgl Bank: {$group['tgl_bank']}\n";
        echo "- Jumlah Kontainer: {$group['count']}\n";
        echo "- Total Amount: Rp " . number_format($group['total_amount'], 2, ',', '.') . "\n";
        echo "- Groups: " . implode(', ', $group['groups']) . "\n";
        echo "- Kontainer: " . implode(', ', $group['kontainers']) . "\n";
        echo "\n";

        $totalKontainerMultiple += $group['count'];
        $counter++;
    }

    echo "STATISTIK GROUP EFISIEN:\n";
    echo "- Jumlah pranota efisien: " . count($multipleKontainer) . "\n";
    echo "- Total kontainer dalam group efisien: {$totalKontainerMultiple}\n";
    echo "- Rata-rata kontainer per pranota efisien: " . round($totalKontainerMultiple / count($multipleKontainer), 2) . "\n";
    echo "- Penghematan: " . ($totalKontainerMultiple - count($multipleKontainer)) . " pranota\n\n";
}

if (!empty($singleKontainer)) {
    echo "=== CONTOH GROUP DENGAN 1 KONTAINER ===\n";
    $singleCounter = 1;
    foreach (array_slice($singleKontainer, 0, 10) as $groupKey => $group) {
        echo "{$singleCounter}. {$group['kontainers'][0]} - Invoice: {$group['no_invoice_vendor']}, Bank: {$group['no_bank']}\n";
        $singleCounter++;
    }
    if (count($singleKontainer) > 10) {
        echo "... dan " . (count($singleKontainer) - 10) . " pranota single kontainer lainnya\n";
    }
    echo "\n";
}

echo "=== REKOMENDASI IMPLEMENTASI ===\n";
echo "1. PRIORITAS TINGGI - Group dengan multiple kontainer:\n";
echo "   - Buat " . count($multipleKontainer) . " pranota untuk {$totalKontainerMultiple} kontainer\n";
echo "   - Hemat " . ($totalKontainerMultiple - count($multipleKontainer)) . " pranota\n\n";

echo "2. PRIORITAS RENDAH - Group dengan single kontainer:\n";
echo "   - " . count($singleKontainer) . " kontainer akan menjadi " . count($singleKontainer) . " pranota terpisah\n";
echo "   - Tidak ada penghematan pranota\n\n";

echo "3. TOTAL PENGHEMATAN:\n";
echo "   - Tanpa grouping: {$kontainerDenganDataLengkap} pranota\n";
echo "   - Dengan grouping: " . count($groupedData) . " pranota\n";
echo "   - Penghematan: " . ($kontainerDenganDataLengkap - count($groupedData)) . " pranota\n\n";

// Generate SQL untuk testing dengan group yang efisien
if (!empty($multipleKontainer)) {
    echo "=== SQL UNTUK TESTING GROUP EFISIEN ===\n";
    echo "-- Membuat sample data untuk group dengan multiple kontainer\n\n";

    $testCounter = 1;
    foreach (array_slice($multipleKontainer, 0, 3) as $groupKey => $group) {
        echo "-- Test Group {$testCounter}: {$group['count']} kontainer dengan invoice {$group['no_invoice_vendor']}\n";

        foreach ($group['kontainers'] as $kontainer) {
            $avgAmount = round($group['total_amount'] / $group['count']);
            echo "INSERT INTO daftar_tagihan_kontainer_sewa (kontainer, no_invoice_vendor, tgl_invoice_vendor, no_bank, tgl_bank, grand_total, status_pranota, supplier, created_at, updated_at) VALUES\n";
            echo "('{$kontainer}', '{$group['no_invoice_vendor']}', ";
            echo $group['tgl_invoice_vendor'] ? "'{$group['tgl_invoice_vendor']}'" : "NULL";
            echo ", '{$group['no_bank']}', ";
            echo $group['tgl_bank'] ? "'{$group['tgl_bank']}'" : "NULL";
            echo ", {$avgAmount}, 'pending', 'ZONA', NOW(), NOW());\n";
        }
        echo "\n";
        $testCounter++;
    }
}

echo "=== SELESAI ANALISIS ===\n";
