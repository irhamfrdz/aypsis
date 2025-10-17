<?php

/**
 * Test Currency Conversion
 * Untuk memastikan format currency di JavaScript sama dengan yang diterima di PHP
 */

echo "=== TEST CURRENCY CONVERSION ===\n\n";

// Simulasi nilai yang dikirim dari form
$testValues = [
    '1.234.567,89',  // Format Indonesia dari view
    '1234567.89',    // Format numerik biasa
    '1,234,567.89',  // Format US (jika ada)
    '0',
    '',
    null,
];

echo "Test 1: Konversi format Indonesia ke numerik\n";
echo "------------------------------------------------\n";

foreach ($testValues as $value) {
    echo "Input: " . var_export($value, true) . "\n";

    // Simulasi parsing seperti di JavaScript getNumericValue()
    if (!$value || $value === '' || $value === null) {
        $numeric = 0;
    } else {
        $stringValue = trim((string)$value);
        $stringValue = preg_replace('/Rp\s*/i', '', $stringValue);
        // Remove dots (thousands separator)
        $stringValue = str_replace('.', '', $stringValue);
        // Replace comma with dot (decimal)
        $stringValue = str_replace(',', '.', $stringValue);

        $numeric = (float) $stringValue;
    }

    echo "Output: $numeric\n";
    echo "Type: " . gettype($numeric) . "\n\n";
}

echo "\nTest 2: Format numerik ke format Indonesia\n";
echo "------------------------------------------------\n";

$testNumbers = [1234567.89, 0, 1000000, 500.5];

foreach ($testNumbers as $num) {
    $formatted = number_format($num, 2, ',', '.');
    echo "Input: $num\n";
    echo "Output: $formatted\n\n";
}

echo "\nTest 3: Simulasi auto-calculation di controller\n";
echo "------------------------------------------------\n";

$data = [
    'dpp' => 1000000,
    'dpp_nilai_lain' => 0,
    'ppn' => 0,
    'pph' => 0,
    'grand_total' => 0,
];

echo "Data awal:\n";
print_r($data);

// Auto-calculate seperti di controller (LOGIKA LAMA - SALAH)
echo "\n=== LOGIKA LAMA (SALAH) ===\n";
$dataOld = $data;
if (!isset($dataOld['dpp_nilai_lain']) || $dataOld['dpp_nilai_lain'] === null || $dataOld['dpp_nilai_lain'] === '') {
    $dataOld['dpp_nilai_lain'] = round($dataOld['dpp'] * 11 / 12, 2);
    echo "Auto-calculated dpp_nilai_lain: {$dataOld['dpp_nilai_lain']}\n";
}
// Problem: 0 dianggap sama dengan null/empty, jadi akan di-overwrite!

echo "\n=== LOGIKA BARU (BENAR) ===\n";
$dataNew = $data;
if ($dataNew['dpp_nilai_lain'] == 0 && $dataNew['dpp'] > 0) {
    $dataNew['dpp_nilai_lain'] = round($dataNew['dpp'] * 11 / 12, 2);
    echo "Auto-calculated dpp_nilai_lain: {$dataNew['dpp_nilai_lain']}\n";
}
// Hanya calculate jika benar-benar 0 DAN ada nilai DPP

echo "\n=== SELESAI ===\n";
