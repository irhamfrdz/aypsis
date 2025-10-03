<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\Auth;

echo "=== DEBUG cleanDpeFormatData ===\n\n";

// Login as admin
Auth::loginUsingId(1);

// Simulate the $data array dari CSV (Row 2)
$data = [
    'Group' => '',
    'Vendor' => 'DPE',
    'Nomor Kontainer' => 'CBHU3952697',
    'Size' => '20',
    'Tanggal Awal' => '24-01-2025',
    'Tanggal Akhir' => '23-02-2025',
    'Periode' => '1',
    'Masa' => 'Periode 1',
    'Tarif' => 'Bulanan',
    'Status' => 'ongoing',
    'DPP' => '775000.00',
    'Adjustment' => '0.00',
    'DPP Nilai Lain' => '0.00',
    'PPN' => '85250.00',
    'PPH' => '15500.00',
    'Grand Total' => '844750.00',
    'Status Pranota' => '',
    'Pranota ID' => '',
];

echo "Input data array:\n";
print_r($data);
echo "\n";

// Simulate getValue function
$getValue = function($key) use ($data) {
    echo "  getValue('$key'):\n";

    // First try exact match
    if (isset($data[$key])) {
        echo "    -> Found exact match: '{$data[$key]}'\n";
        return $data[$key];
    }
    echo "    -> No exact match\n";

    // Then try with possible BOM variations
    $bomVariations = [
        "\xEF\xBB\xBF" . $key,    // UTF-8 BOM
        "\u{FEFF}" . $key,        // Unicode BOM (if supported)
    ];

    foreach ($bomVariations as $bomKey) {
        if (isset($data[$bomKey])) {
            echo "    -> Found BOM match\n";
            return $data[$bomKey];
        }
    }

    // Finally search through all keys for one that ends with our target
    foreach (array_keys($data) as $dataKey) {
        // Remove any BOM characters and check if it matches
        $cleanKey = preg_replace('/^[\x{FEFF}\x{EF}\x{BB}\x{BF}]+/u', '', $dataKey);
        if ($cleanKey === $key) {
            echo "    -> Found via clean key match: '{$data[$dataKey]}'\n";
            return $data[$dataKey];
        }

        // Also check if original key ends with target (loose matching)
        if (strlen($dataKey) >= strlen($key) && substr($dataKey, -strlen($key)) === $key) {
            echo "    -> Found via suffix match: '{$data[$dataKey]}'\n";
            return $data[$dataKey];
        }
    }

    echo "    -> Not found, returning empty\n";
    return '';
};

// Test getValue for 'vendor' and 'Vendor'
echo "Testing getValue:\n";
$vendor1 = $getValue('vendor');
echo "Result for 'vendor': '$vendor1'\n\n";

$vendor2 = $getValue('Vendor');
echo "Result for 'Vendor': '$vendor2'\n\n";

// Simulate the actual code logic
echo "Simulating actual code logic:\n";
$vendor = $getValue('Vendor') ?: ($getValue('vendor') ?: 'DPE');
$vendor = trim($vendor);
if (empty($vendor)) {
    $vendor = 'DPE';
}
echo "Final vendor value: '$vendor'\n\n";

echo "=== END DEBUG ===\n";
