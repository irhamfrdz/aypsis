<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debug NIK Field Issue ===\n\n";

// Check SuratJalan fields related to karyawan/NIK
echo "1. Checking SuratJalan fields related to karyawan/NIK...\n";
$suratJalan = \App\Models\SuratJalan::first();
if ($suratJalan) {
    $attributes = $suratJalan->getAttributes();
    echo "Available fields in SuratJalan:\n";
    foreach ($attributes as $field => $value) {
        if (strpos($field, 'karyawan') !== false || strpos($field, 'nik') !== false || strpos($field, 'supir') !== false) {
            echo "- {$field}: " . ($value ?: 'NULL') . "\n";
        }
    }
}

echo "\n2. Checking if there's a Karyawan model/relationship...\n";
if (class_exists('\App\Models\Karyawan')) {
    echo "✅ Karyawan model exists\n";
    $karyawan = \App\Models\Karyawan::first();
    if ($karyawan) {
        echo "Sample Karyawan data:\n";
        $attributes = $karyawan->getAttributes();
        foreach ($attributes as $field => $value) {
            echo "- {$field}: " . ($value ?: 'NULL') . "\n";
        }
    }
} else {
    echo "❌ Karyawan model not found\n";
}

echo "\n3. Checking SuratJalan relationships...\n";
$suratJalan = \App\Models\SuratJalan::with('karyawanRelation')->first();
if ($suratJalan && method_exists($suratJalan, 'karyawanRelation')) {
    echo "✅ karyawanRelation exists\n";
    if ($suratJalan->karyawanRelation) {
        echo "Karyawan data: " . print_r($suratJalan->karyawanRelation->toArray(), true);
    } else {
        echo "⚠️ karyawanRelation is null\n";
    }
} else {
    echo "❌ karyawanRelation method not found\n";
}

echo "\n4. Checking current field mapping in SuratJalan...\n";
if ($suratJalan) {
    echo "Current karyawan field value: " . ($suratJalan->karyawan ?: 'NULL') . "\n";
    echo "Field type: " . gettype($suratJalan->karyawan) . "\n";
    
    // Check if it's an ID that needs to be resolved
    if (is_numeric($suratJalan->karyawan)) {
        echo "⚠️ karyawan field contains numeric value (possibly ID): {$suratJalan->karyawan}\n";
        
        // Try to find karyawan by ID
        if (class_exists('\App\Models\Karyawan')) {
            $karyawanData = \App\Models\Karyawan::find($suratJalan->karyawan);
            if ($karyawanData) {
                echo "✅ Found karyawan data by ID:\n";
                echo "- Name: " . ($karyawanData->name ?? $karyawanData->nama ?? 'N/A') . "\n";
                echo "- NIK: " . ($karyawanData->nik ?? 'N/A') . "\n";
            }
        }
    }
}

echo "\n5. Testing UangJalan with SuratJalan relationship...\n";
$uangJalan = \App\Models\UangJalan::with('suratJalan.karyawanRelation')->first();
if ($uangJalan && $uangJalan->suratJalan) {
    echo "UangJalan -> SuratJalan found\n";
    echo "- Surat Jalan ID: " . $uangJalan->suratJalan->id . "\n";
    echo "- Karyawan field: " . ($uangJalan->suratJalan->karyawan ?: 'NULL') . "\n";
    
    if ($uangJalan->suratJalan->karyawanRelation ?? null) {
        echo "- NIK from relationship: " . ($uangJalan->suratJalan->karyawanRelation->nik ?? 'N/A') . "\n";
    } else {
        echo "- No karyawanRelation found\n";
    }
}