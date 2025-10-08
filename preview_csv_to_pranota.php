<?php
/**
 * Preview import CSV to pranota
 * Menampilkan bagaimana data akan dikelompokkan berdasarkan invoice vendor
 */

echo "=== PREVIEW CSV TO PRANOTA GROUPING ===\n\n";

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
echo "📋 Headers detected: " . count($headers) . " columns\n\n";

// Group data by invoice vendor
$invoiceGroups = [];
$rowCount = 0;
$processedCount = 0;
$skipCount = 0;
$noBankCount = 0;

echo "📊 Analyzing CSV data...\n";

while (($row = fgetcsv($handle, 1000, ';')) !== false) {
    $rowCount++;
    
    // Skip header row or incomplete rows
    if ($rowCount === 1 || count($row) < 20) {
        continue;
    }
    
    // Extract key fields
    $group = trim($row[0] ?? '');
    $kontainer = trim($row[1] ?? '');
    $noInvoiceVendor = trim($row[17] ?? '');
    $tglInvVendor = trim($row[18] ?? '');
    $noBank = trim($row[19] ?? '');
    $tglBank = trim($row[20] ?? '');
    $dpp = trim($row[9] ?? '');
    $adjustment = trim($row[12] ?? '');
    
    if (empty($noInvoiceVendor)) {
        $skipCount++;
        continue;
    }
    
    if (empty($noBank) || $noBank === '-') {
        $noBankCount++;
        continue;
    }
    
    // Clean DPP value
    $dppValue = (float)str_replace([' ', '.', ','], '', $dpp);
    
    // Clean adjustment value
    $adjustmentValue = 0;
    if ($adjustment !== '-' && !empty($adjustment)) {
        $adjustmentValue = (float)str_replace([' ', '.', ',', '-'], '', $adjustment);
        if (strpos($adjustment, '-') !== false) {
            $adjustmentValue = -$adjustmentValue;
        }
    }
    
    if (!isset($invoiceGroups[$noInvoiceVendor])) {
        $invoiceGroups[$noInvoiceVendor] = [
            'items' => [],
            'total_dpp' => 0,
            'total_adjustment' => 0,
            'invoice_info' => [
                'no_invoice_vendor' => $noInvoiceVendor,
                'tgl_inv_vendor' => $tglInvVendor,
                'no_bank' => $noBank,
                'tgl_bank' => $tglBank,
            ]
        ];
    }
    
    $invoiceGroups[$noInvoiceVendor]['items'][] = [
        'group' => $group,
        'kontainer' => $kontainer,
        'dpp' => $dppValue,
        'adjustment' => $adjustmentValue
    ];
    
    $invoiceGroups[$noInvoiceVendor]['total_dpp'] += $dppValue;
    $invoiceGroups[$noInvoiceVendor]['total_adjustment'] += $adjustmentValue;
    
    $processedCount++;
}

fclose($handle);

echo "✅ Analysis complete\n\n";

echo "📊 SUMMARY:\n";
echo "━" . str_repeat("━", 50) . "\n";
echo "📋 Total rows in CSV: " . ($rowCount - 1) . "\n";
echo "✅ Valid rows (with invoice & bank): $processedCount\n";
echo "⚠️  Skipped (no invoice): $skipCount\n";
echo "❌ Skipped (no bank info): $noBankCount\n";
echo "📦 Invoice groups to create: " . count($invoiceGroups) . "\n\n";

if (count($invoiceGroups) > 0) {
    echo "📋 PRANOTA PREVIEW:\n";
    echo "━" . str_repeat("━", 90) . "\n";
    printf("%-20s %-12s %-12s %-8s %-12s %-15s\n", 
        'Invoice No', 'Bank No', 'Bank Date', 'Items', 'Total DPP', 'Adjustment');
    echo "━" . str_repeat("━", 90) . "\n";
    
    $grandTotalDpp = 0;
    $grandTotalAdjustment = 0;
    
    foreach ($invoiceGroups as $invoiceNo => $group) {
        $itemCount = count($group['items']);
        $totalDpp = $group['total_dpp'];
        $totalAdjustment = $group['total_adjustment'];
        
        $grandTotalDpp += $totalDpp;
        $grandTotalAdjustment += $totalAdjustment;
        
        printf("%-20s %-12s %-12s %-8s %-12s %-15s\n",
            substr($invoiceNo, 0, 19),
            $group['invoice_info']['no_bank'],
            $group['invoice_info']['tgl_bank'],
            $itemCount . ' item' . ($itemCount > 1 ? 's' : ''),
            'Rp ' . number_format($totalDpp, 0),
            'Rp ' . number_format($totalAdjustment, 0)
        );
    }
    
    echo "━" . str_repeat("━", 90) . "\n";
    printf("%-44s %-8s %-12s %-15s\n",
        'TOTAL',
        $processedCount . ' items',
        'Rp ' . number_format($grandTotalDpp, 0),
        'Rp ' . number_format($grandTotalAdjustment, 0)
    );
    echo "━" . str_repeat("━", 90) . "\n\n";
    
    // Show detailed breakdown for first few groups
    echo "📝 DETAILED BREAKDOWN (First 3 groups):\n";
    echo "━" . str_repeat("━", 70) . "\n";
    
    $count = 0;
    foreach ($invoiceGroups as $invoiceNo => $group) {
        if ($count >= 3) break;
        
        echo "\n🏷️  Invoice: $invoiceNo\n";
        echo "   📅 Invoice Date: {$group['invoice_info']['tgl_inv_vendor']}\n";
        echo "   🏦 Bank: {$group['invoice_info']['no_bank']} ({$group['invoice_info']['tgl_bank']})\n";
        echo "   📦 Items (" . count($group['items']) . "):\n";
        
        foreach ($group['items'] as $item) {
            echo "      • {$item['group']} - {$item['kontainer']} | DPP: " . number_format($item['dpp'], 0) . 
                 " | Adj: " . number_format($item['adjustment'], 0) . "\n";
        }
        
        $count++;
    }
    
    echo "\n💡 NEXT STEPS:\n";
    echo "━" . str_repeat("━", 50) . "\n";
    echo "1. Jalankan: php import_csv_to_pranota.php\n";
    echo "2. Akan membuat " . count($invoiceGroups) . " pranota baru\n";
    echo "3. Dengan total $processedCount tagihan kontainer\n";
    echo "4. Total nilai: Rp " . number_format($grandTotalDpp + $grandTotalAdjustment, 0) . "\n\n";
} else {
    echo "❌ Tidak ada data valid untuk diimpor!\n";
    echo "   Pastikan file CSV memiliki kolom nomor invoice vendor dan nomor bank.\n\n";
}

echo "=== PREVIEW SELESAI ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";