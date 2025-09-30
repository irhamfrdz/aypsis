<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Test validation
$testData = [
    'modul' => 'TEST_MODULE',
    'nomor_terakhir' => '000001', // String with leading zeros
    'keterangan' => 'Test description'
];

$validator = Validator::make($testData, [
    'modul' => 'required|string|max:100',
    'nomor_terakhir' => 'required|numeric|min:0',
    'keterangan' => 'nullable|string|max:1000'
]);

if ($validator->fails()) {
    echo "❌ Validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "✅ Validation passed!\n";
    echo "Original nomor_terakhir: '{$testData['nomor_terakhir']}' (" . gettype($testData['nomor_terakhir']) . ")\n";

    // Convert to integer
    $converted = (int) $testData['nomor_terakhir'];
    echo "Converted nomor_terakhir: $converted (" . gettype($converted) . ")\n";
}
