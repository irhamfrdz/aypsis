<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Check UangJalan Fields ===\n\n";

// Check UangJalan fields
$uangJalan = \App\Models\UangJalan::first();
if ($uangJalan) {
    echo "Available fields in UangJalan:\n";
    $attributes = $uangJalan->getAttributes();
    foreach ($attributes as $field => $value) {
        echo "- {$field}: " . ($value ?: 'NULL') . "\n";
    }
} else {
    echo "No UangJalan found\n";
}

echo "\n=== Check SuratJalan Fields ===\n\n";

// Check SuratJalan fields  
$suratJalan = \App\Models\SuratJalan::first();
if ($suratJalan) {
    echo "Available fields in SuratJalan:\n";
    $attributes = $suratJalan->getAttributes();
    foreach ($attributes as $field => $value) {
        if (strpos($field, 'bukti') !== false || strpos($field, 'nomor') !== false || strpos($field, 'no_') !== false) {
            echo "- {$field}: " . ($value ?: 'NULL') . "\n";
        }
    }
} else {
    echo "No SuratJalan found\n";
}