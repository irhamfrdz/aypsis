<?php

/**
 * Test Enhanced CSV Import Format Detection
 *
 * This script tests the enhanced CSV import functionality with flexible column matching
 */

echo "=== Testing Enhanced CSV Import Format Detection ===\n\n";

// Test CSV headers with various format variations
$testHeaders = [
    // Standard formats (should work)
    ['Group', 'Periode', 'Nomor Kontainer', 'keterangan'],
    ['group', 'periode', 'nomor_kontainer', 'keterangan'],
    ['GROUP', 'PERIODE', 'KONTAINER', 'KETERANGAN'],

    // Vendor invoice formats (should work)
    ['No.InvoiceVendor', 'No.Bank', 'Nomor Kontainer'],
    ['InvoiceVendor', 'Bank', 'Kontainer'],
    ['Invoice Vendor', 'No Bank', 'nomor_kontainer'],

    // Mixed case variations (should work)
    ['Group', 'periode', 'Nomor Kontainer'],
    ['No.InvoiceVendor', 'bank', 'kontainer'],

    // Invalid formats (should fail)
    ['Random', 'Column', 'Names'],
    ['Group', 'Missing_Periode'],
    ['No.InvoiceVendor', 'Missing_Bank'],
];

// Simulate the flexible column detection logic
function testColumnDetection($header) {
    echo "Testing header: [" . implode(', ', $header) . "]\n";

    // Helper function to check if column exists (case-insensitive and flexible)
    $findColumn = function($possibleNames) use ($header) {
        foreach ($possibleNames as $name) {
            foreach ($header as $col) {
                if (strcasecmp(trim($col), trim($name)) === 0) {
                    return true;
                }
            }
        }
        return false;
    };

    // Define possible column variations
    $groupColumns = ['group', 'Group', 'GROUP'];
    $periodeColumns = ['periode', 'Periode', 'PERIODE'];
    $kontainerColumns = ['nomor_kontainer', 'Nomor Kontainer', 'Nomor_Kontainer', 'NOMOR_KONTAINER', 'kontainer', 'Kontainer'];
    $invoiceVendorColumns = ['No.InvoiceVendor', 'No InvoiceVendor', 'InvoiceVendor', 'Invoice Vendor', 'invoice_vendor'];
    $bankColumns = ['No.Bank', 'No Bank', 'Bank', 'NoBank', 'no_bank'];

    // Check for vendor invoice format first (higher priority)
    $hasInvoiceVendor = $findColumn($invoiceVendorColumns);
    $hasBank = $findColumn($bankColumns);
    $hasKontainer = $findColumn($kontainerColumns);

    if ($hasInvoiceVendor && $hasBank && $hasKontainer) {
        echo "✅ VALID - Vendor Invoice Grouping Mode\n";
        echo "   Detected: Invoice=✓, Bank=✓, Kontainer=✓\n";
        return 'vendor_invoice';
    } else {
        // Check for standard group + periode format
        $hasGroup = $findColumn($groupColumns);
        $hasPeriode = $findColumn($periodeColumns);

        if ($hasGroup && $hasPeriode && $hasKontainer) {
            echo "✅ VALID - Standard Group + Periode Mode\n";
            echo "   Detected: Group=✓, Periode=✓, Kontainer=✓\n";
            return 'group_periode';
        } else {
            echo "❌ INVALID - Missing required columns\n";
            echo "   Status: Group=" . ($hasGroup ? '✓' : '✗') .
                 ", Periode=" . ($hasPeriode ? '✓' : '✗') .
                 ", Kontainer=" . ($hasKontainer ? '✓' : '✗') .
                 ", Invoice=" . ($hasInvoiceVendor ? '✓' : '✗') .
                 ", Bank=" . ($hasBank ? '✓' : '✗') . "\n";
            return 'invalid';
        }
    }
}

// Test each header format
$validCount = 0;
$totalCount = count($testHeaders);

foreach ($testHeaders as $index => $header) {
    echo "\n" . ($index + 1) . ". ";
    $result = testColumnDetection($header);
    if ($result !== 'invalid') {
        $validCount++;
    }
    echo "\n";
}

echo "=== Test Summary ===\n";
echo "Total tests: {$totalCount}\n";
echo "Valid formats: {$validCount}\n";
echo "Invalid formats: " . ($totalCount - $validCount) . "\n";
echo "Success rate: " . round(($validCount / $totalCount) * 100, 1) . "%\n\n";

// Test sample data extraction
echo "=== Testing Data Extraction ===\n\n";

$sampleData = [
    // Standard format data
    [
        'header' => ['Group', 'Periode', 'Nomor Kontainer', 'keterangan'],
        'rows' => [
            ['1', '202412', 'TEMU1234567', 'Test container 1'],
            ['1', '202412', 'TEMU2345678', 'Test container 2'],
            ['2', '202412', 'TEMU3456789', 'Test container 3'],
        ]
    ],
    // Vendor invoice format data
    [
        'header' => ['No.InvoiceVendor', 'No.Bank', 'Nomor Kontainer'],
        'rows' => [
            ['INV001', 'BNK001', 'TEMU1234567'],
            ['INV001', 'BNK001', 'TEMU2345678'],
            ['INV002', 'BNK001', 'TEMU3456789'],
        ]
    ]
];

function testDataExtraction($testData) {
    $header = $testData['header'];
    echo "Testing data extraction for: [" . implode(', ', $header) . "]\n";

    // Determine mode
    $mode = testColumnDetection($header);
    if ($mode === 'invalid') {
        echo "❌ Cannot test data extraction - invalid format\n\n";
        return;
    }

    // Create flexible column mapper
    $getColumnValue = function($possibleNames, $row) use ($header) {
        foreach ($possibleNames as $name) {
            foreach ($header as $index => $col) {
                if (strcasecmp(trim($col), trim($name)) === 0) {
                    return isset($row[$index]) ? trim($row[$index]) : '';
                }
            }
        }
        return '';
    };

    // Define column variations
    $groupColumns = ['group', 'Group', 'GROUP'];
    $periodeColumns = ['periode', 'Periode', 'PERIODE'];
    $kontainerColumns = ['nomor_kontainer', 'Nomor Kontainer', 'Nomor_Kontainer', 'NOMOR_KONTAINER', 'kontainer', 'Kontainer'];
    $invoiceVendorColumns = ['No.InvoiceVendor', 'No InvoiceVendor', 'InvoiceVendor', 'Invoice Vendor', 'invoice_vendor'];
    $bankColumns = ['No.Bank', 'No Bank', 'Bank', 'NoBank', 'no_bank'];

    // Process sample rows
    $groupedData = [];

    foreach ($testData['rows'] as $rowIndex => $row) {
        $nomorKontainer = $getColumnValue($kontainerColumns, $row);

        if ($mode === 'vendor_invoice') {
            $invoiceVendor = $getColumnValue($invoiceVendorColumns, $row);
            $bankNumber = $getColumnValue($bankColumns, $row);
            $groupKey = $invoiceVendor . '_' . $bankNumber;
            $groupLabel = "Invoice: {$invoiceVendor} | Bank: {$bankNumber}";
        } else {
            $group = $getColumnValue($groupColumns, $row);
            $periode = $getColumnValue($periodeColumns, $row);
            $groupKey = "{$group}_{$periode}";
            $groupLabel = "Group: {$group} | Periode: {$periode}";
        }

        if (!isset($groupedData[$groupKey])) {
            $groupedData[$groupKey] = [
                'label' => $groupLabel,
                'kontainers' => []
            ];
        }

        $groupedData[$groupKey]['kontainers'][] = $nomorKontainer;
    }

    // Show grouping results
    echo "Grouping Results:\n";
    foreach ($groupedData as $key => $data) {
        echo "  📦 {$data['label']}\n";
        echo "     Kontainers: " . implode(', ', $data['kontainers']) . " (" . count($data['kontainers']) . " items)\n";
    }

    $totalGroups = count($groupedData);
    $totalKontainers = array_sum(array_map(function($g) { return count($g['kontainers']); }, $groupedData));
    $efficiency = $totalKontainers > 0 ? round((($totalKontainers - $totalGroups) / $totalKontainers) * 100, 1) : 0;

    echo "  📊 Summary: {$totalKontainers} kontainers → {$totalGroups} groups (Efficiency: {$efficiency}%)\n\n";
}

foreach ($sampleData as $testData) {
    testDataExtraction($testData);
}

echo "✅ Enhanced CSV Import Format Detection Test Completed!\n";
