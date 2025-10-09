<?php
/**
 * Simulasi lengkap cleanDpeFormatData process
 */

echo "=== SIMULASI CLEANDPEFORMATDATA PROCESS ===\n\n";

// Helper functions dari controller
function getValue($row, $headers, $columnName) {
    $index = array_search($columnName, $headers);
    return $index !== false && isset($row[$index]) ? $row[$index] : null;
}

function cleanDpeNumber($value) {
    if (empty($value) || trim($value) === '' || trim($value) === '-') {
        return 0;
    }

    $value = trim($value);
    $isNegative = false;

    if (strpos($value, '-') !== false) {
        $isNegative = true;
        $value = str_replace('-', '', $value);
    }

    $cleaned = preg_replace('/[^\d.,]/', '', $value);
    $cleaned = str_replace(',', '', $cleaned);

    $result = (float) $cleaned;
    return $isNegative ? -$result : $result;
}

function cleanDpeStatus($status) {
    $status = strtolower(trim($status));

    if (in_array($status, ['active', 'aktif', 'ongoing', 'berlangsung'])) {
        return 'ongoing';
    }

    if (in_array($status, ['complete', 'selesai', 'finished', 'done'])) {
        return 'complete';
    }

    if (in_array($status, ['pending', 'menunggu', 'waiting'])) {
        return 'pending';
    }

    // Default to ongoing if unrecognized
    return 'ongoing';
}

function cleanVendor($vendor) {
    $vendor = strtoupper(trim($vendor));

    // Map common variations
    if (in_array($vendor, ['DPE', 'PT DPE', 'PT.DPE', 'PT. DPE'])) {
        return 'DPE';
    }

    if (in_array($vendor, ['ZONA', 'PT ZONA', 'PT.ZONA', 'PT. ZONA'])) {
        return 'ZONA';
    }

    return $vendor;
}

function cleanSize($size) {
    $size = trim($size);

    // Extract numeric part
    preg_match('/(\d+)/', $size, $matches);

    if (!empty($matches[1])) {
        return $matches[1];
    }

    // Fallback
    return $size;
}

function parseDate($dateString) {
    if (empty($dateString)) {
        return null;
    }

    try {
        $date = new DateTime($dateString);
        return $date->format('Y-m-d');
    } catch (Exception $e) {
        return null;
    }
}

$csvFile = 'Zona_SIAP_IMPORT_FINAL_TARIF_BENAR_COMMA.csv';

if (!file_exists($csvFile)) {
    echo "File tidak ditemukan: $csvFile\n";
    exit(1);
}

// Baca CSV
$handle = fopen($csvFile, 'r');
$headers = fgetcsv($handle, 1000, ',');

echo "Headers: " . implode(' | ', $headers) . "\n\n";

// Process record dengan adjustment
$recordFound = false;
$rowCount = 0;

while (($row = fgetcsv($handle, 1000, ',')) !== false && !$recordFound) {
    $rowCount++;

    $adjustmentValue = getValue($row, $headers, 'Adjustment');

    if (!empty($adjustmentValue) && $adjustmentValue != '0' && $adjustmentValue != '0.00') {
        $recordFound = true;

        echo "=== PROCESSING RECORD $rowCount ===\n\n";

        // Simulasi cleanDpeFormatData
        echo "Step 1: Raw data extraction\n";
        $vendor = cleanVendor(getValue($row, $headers, 'Vendor'));
        $nomor_kontainer = strtoupper(trim(getValue($row, $headers, 'Nomor Kontainer')));
        $size = cleanSize(getValue($row, $headers, 'Size'));
        $tanggal_awal = parseDate(getValue($row, $headers, 'Tanggal Awal'));
        $tanggal_akhir = parseDate(getValue($row, $headers, 'Tanggal Akhir'));
        $tarif = trim(getValue($row, $headers, 'Tarif'));
        $periode = getValue($row, $headers, 'Periode') ?: 1;
        $group = getValue($row, $headers, 'Group');
        $status = cleanDpeStatus(getValue($row, $headers, 'Status') ?: 'ongoing');

        echo "  Vendor: $vendor\n";
        echo "  Container: $nomor_kontainer\n";
        echo "  Size: $size\n";
        echo "  Dates: $tanggal_awal to $tanggal_akhir\n";
        echo "  Tarif: $tarif\n";
        echo "  Periode: $periode\n";
        echo "  Group: $group\n";
        echo "  Status: $status\n";

        echo "\nStep 2: Financial data processing\n";

        // DPP value
        $dppValue = getValue($row, $headers, 'DPP');
        $dpp = !empty($dppValue) ? cleanDpeNumber($dppValue) : 0;
        echo "  DPP raw: '$dppValue' -> clean: $dpp\n";

        // Adjustment value - KEY POINT HERE
        $adjustmentRaw = trim(getValue($row, $headers, 'Adjustment') ?: getValue($row, $headers, 'adjustment'));
        $adjustment = !empty($adjustmentRaw) ? cleanDpeNumber($adjustmentRaw) : 0;
        echo "  Adjustment raw: '$adjustmentRaw' -> clean: $adjustment\n";

        // Adjusted DPP
        $adjustedDpp = $dpp + $adjustment;
        echo "  Adjusted DPP: $dpp + $adjustment = $adjustedDpp\n";

        // PPN calculation
        $ppnValue = getValue($row, $headers, 'PPN') ?: getValue($row, $headers, 'ppn');
        $ppn = !empty($ppnValue) ? cleanDpeNumber($ppnValue) : round($adjustedDpp * 0.11, 2);
        echo "  PPN: " . (empty($ppnValue) ? "calculated 11% = $ppn" : "from CSV = $ppn") . "\n";

        // PPH calculation
        $pphValue = getValue($row, $headers, 'PPH') ?: getValue($row, $headers, 'pph');
        $pph = !empty($pphValue) ? cleanDpeNumber($pphValue) : round($adjustedDpp * 0.02, 2);
        echo "  PPH: " . (empty($pphValue) ? "calculated 2% = $pph" : "from CSV = $pph") . "\n";

        // Grand total
        $grandTotal = $adjustedDpp + $ppn - $pph;
        echo "  Grand Total: $adjustedDpp + $ppn - $pph = $grandTotal\n";

        echo "\nStep 3: Final cleaned data\n";
        $cleaned = [
            'vendor' => $vendor,
            'nomor_kontainer' => $nomor_kontainer,
            'size' => $size,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'tarif' => $tarif,
            'periode' => $periode,
            'group' => $group,
            'status' => $status,
            'dpp' => $dpp,
            'adjustment' => $adjustment, // KEY: Should be preserved here
            'ppn' => $ppn,
            'pph' => $pph,
            'grand_total' => $grandTotal,
        ];

        foreach ($cleaned as $key => $value) {
            if ($key === 'adjustment') {
                echo "  ★ $key: $value ← KEY FIELD\n";
            } else {
                echo "  $key: $value\n";
            }
        }

        echo "\n=== ANALYSIS ===\n";
        if ($adjustment != 0) {
            echo "✓ Adjustment properly processed: $adjustment\n";
        } else {
            echo "✗ Adjustment lost during processing!\n";
        }
    }
}

fclose($handle);

if (!$recordFound) {
    echo "No records with adjustment found\n";
}
