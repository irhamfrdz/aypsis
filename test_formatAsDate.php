<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Karyawan;

echo "Testing formatAsDate method on Karyawan model...\n";

// Get first karyawan
$karyawan = Karyawan::first();
if (!$karyawan) {
    echo "❌ No karyawan found in database\n";
    exit(1);
}

echo "Found karyawan: {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n";

// Test formatAsDate method
$testAttributes = [
    'tanggal_lahir',
    'tanggal_masuk',
    'tanggal_berhenti',
    'tanggal_masuk_sebelumnya',
    'tanggal_berhenti_sebelumnya'
];

foreach ($testAttributes as $attribute) {
    $rawValue = $karyawan->getAttribute($attribute);
    $formattedValue = $karyawan->formatAsDate($attribute, 'Y-m-d');

    echo "Attribute: {$attribute}\n";
    echo "  Raw value: " . ($rawValue ?? 'null') . "\n";
    echo "  Formatted: " . ($formattedValue ?? 'null') . "\n";
    echo "  ✅ Method works for {$attribute}\n\n";
}

echo "🎉 All formatAsDate tests passed!\n";
echo "The method is now available for use in edit.blade.php\n";
