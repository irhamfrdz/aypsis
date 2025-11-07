<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Table Structure Analysis ===\n\n";

echo "1. SuratJalans table columns:\n";
$columns = DB::select('DESCRIBE surat_jalans');
foreach ($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\n2. UangJalans table columns:\n";
$columns = DB::select('DESCRIBE uang_jalans');  
foreach ($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\n3. Sample data analysis:\n";
$uangJalan = \App\Models\UangJalan::with('suratJalan')->first();
if ($uangJalan) {
    echo "UangJalan fields with potential NIK:\n";
    foreach ($uangJalan->getAttributes() as $field => $value) {
        if (strpos(strtolower($field), 'nik') !== false || 
            strpos(strtolower($field), 'karyawan') !== false ||
            strpos($field, 'supir') !== false) {
            echo "- UangJalan.{$field}: " . ($value ?: 'NULL') . "\n";
        }
    }
    
    if ($uangJalan->suratJalan) {
        echo "\nSuratJalan fields with potential NIK:\n";
        foreach ($uangJalan->suratJalan->getAttributes() as $field => $value) {
            if (strpos(strtolower($field), 'nik') !== false || 
                strpos(strtolower($field), 'karyawan') !== false ||
                strpos($field, 'supir') !== false ||
                strpos($field, 'kenek') !== false) {
                echo "- SuratJalan.{$field}: " . ($value ?: 'NULL') . "\n";
            }
        }
    }
}