<?php

/**
 * Test untuk mendeteksi kenapa nilai edit tidak sesuai yang tersimpan
 */

echo "=== TEST EDIT SAVE ISSUE ===\n\n";

// Data dari screenshot
$originalDB = [
    'dpp' => 882882.00,
    'ppn' => 97117.00,
    'pph' => 17658.00,
    'grand_total' => 962341.00,
];

echo "1. Data Original di Database:\n";
print_r($originalDB);

// Data yang ditampilkan di form (dengan number_format)
echo "\n2. Data Ditampilkan di Form (number_format):\n";
foreach ($originalDB as $key => $value) {
    $formatted = number_format($value, 2, ',', '.');
    echo "$key: $formatted\n";
}

// Simulasi: User edit DPP jadi 882.882,00 (tidak berubah, tapi re-input)
$userInput = '882.882,00';

echo "\n3. User Input (dari display field): $userInput\n";

// Simulasi parsing JavaScript getNumericValue()
function jsGetNumericValue($formattedValue) {
    if (!$formattedValue || $formattedValue === '' || $formattedValue === null) {
        return 0;
    }

    $stringValue = trim((string)$formattedValue);
    $stringValue = preg_replace('/Rp\s*/i', '', $stringValue);

    // Count dots and commas
    $dotCount = substr_count($stringValue, '.');
    $commaCount = substr_count($stringValue, ',');

    echo "   - Dots: $dotCount, Commas: $commaCount\n";

    // Indonesian format: 1.234.567,89 (multiple dots OR single dot for thousands, one comma)
    if ($dotCount > 0 && $commaCount === 1) {
        echo "   - Detected: Indonesian format with dot thousands and comma decimal\n";
        $stringValue = str_replace('.', '', $stringValue); // Remove thousands
        $stringValue = str_replace(',', '.', $stringValue); // Replace decimal
    }
    // US format or simple decimal: 1234567.89 (one dot)
    else if ($commaCount === 0 && $dotCount === 1) {
        echo "   - Detected: Simple decimal with dot\n";
        // Already correct
    }
    // Only comma: 1234567,89
    else if ($commaCount === 1 && $dotCount === 0) {
        echo "   - Detected: Decimal with comma only\n";
        $stringValue = str_replace(',', '.', $stringValue);
    }
    // Multiple dots, no comma: 1.234.567
    else if ($dotCount > 1 && $commaCount === 0) {
        echo "   - Detected: Thousands with dots only\n";
        $stringValue = str_replace('.', '', $stringValue);
    }
    else {
        echo "   - Detected: Other format, removing all separators\n";
        $stringValue = str_replace(['.', ','], '', $stringValue);
    }

    echo "   - After processing: $stringValue\n";

    $number = (float) $stringValue;
    echo "   - Final numeric value: $number\n";

    return $number;
}

$parsed = jsGetNumericValue($userInput);

echo "\n4. After JavaScript Parse: $parsed\n";

// Simulasi controller validation dan processing
echo "\n5. Controller Processing:\n";

$data = [
    'dpp' => $parsed,
    'dpp_nilai_lain' => 770770.00,
    'ppn' => 97117.00,
    'pph' => 17658.00,
    'grand_total' => 962341.00,
];

echo "Data before auto-calculation:\n";
print_r($data);

// Check auto-calculation logic (LOGIKA BARU)
if ($data['dpp_nilai_lain'] == 0 && $data['dpp'] > 0) {
    $data['dpp_nilai_lain'] = round($data['dpp'] * 11 / 12, 2);
    echo "Auto-calculated dpp_nilai_lain: {$data['dpp_nilai_lain']}\n";
}

if ($data['ppn'] == 0 && $data['dpp_nilai_lain'] > 0) {
    $data['ppn'] = round($data['dpp_nilai_lain'] * 0.12, 2);
    echo "Auto-calculated ppn: {$data['ppn']}\n";
}

if ($data['pph'] == 0 && $data['dpp'] > 0) {
    $data['pph'] = round($data['dpp'] * 0.02, 2);
    echo "Auto-calculated pph: {$data['pph']}\n";
}

if ($data['grand_total'] == 0) {
    $data['grand_total'] = round($data['dpp'] + $data['ppn'] - $data['pph'], 2);
    echo "Auto-calculated grand_total: {$data['grand_total']}\n";
}

echo "\n6. Data yang akan tersimpan di Database:\n";
print_r($data);

echo "\n7. Data setelah tersimpan (number_format lagi):\n";
foreach ($data as $key => $value) {
    $formatted = number_format($value, 2, ',', '.');
    echo "$key: $formatted\n";
}

// Test dari screenshot: nilai yang salah
echo "\n\n=== ANALISIS NILAI YANG SALAH ===\n";
echo "Dari screenshot, DPP tersimpan: Rp 924,924\n";
echo "Original DPP: 882.882,00\n";
echo "Difference: " . (924924 - 882882) . "\n";
echo "Percentage: " . round((924924 / 882882 - 1) * 100, 2) . "%\n";

echo "\nKemungkinan penyebab:\n";
echo "1. Parsing salah: 882.882,00 -> 882882.00 (BENAR)\n";
echo "2. Auto-calculation menimpa nilai (Sudah diperbaiki)\n";
echo "3. Ada proses lain yang mengubah nilai setelah save?\n";

echo "\n=== SELESAI ===\n";
