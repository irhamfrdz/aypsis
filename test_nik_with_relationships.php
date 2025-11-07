<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test NIK Field with Supir/Kenek Relationships ===\n\n";

// Test the new relationships
echo "1. Testing supirKaryawan relationship...\n";
$suratJalan = \App\Models\SuratJalan::with(['supirKaryawan', 'kenekKaryawan'])
    ->whereNotNull('supir')
    ->first();

if ($suratJalan) {
    echo "SuratJalan: {$suratJalan->no_surat_jalan}\n";
    echo "Supir: {$suratJalan->supir}\n";
    echo "Kenek: " . ($suratJalan->kenek ?: 'NULL') . "\n";
    
    if ($suratJalan->supirKaryawan) {
        echo "✅ Supir Karyawan found: {$suratJalan->supirKaryawan->nik} - {$suratJalan->supirKaryawan->nama_lengkap}\n";
    } else {
        echo "❌ No supirKaryawan relationship found\n";
    }
    
    if ($suratJalan->kenekKaryawan) {
        echo "✅ Kenek Karyawan found: {$suratJalan->kenekKaryawan->nik} - {$suratJalan->kenekKaryawan->nama_lengkap}\n";
    } else {
        echo "❌ No kenekKaryawan relationship found\n";
    }
}

echo "\n2. Testing PranotaUangJalan with new relationships...\n";
$pranota = \App\Models\PranotaUangJalan::with([
    'uangJalans.suratJalan.supirKaryawan',
    'uangJalans.suratJalan.kenekKaryawan',
    'creator'
])->first();

if ($pranota) {
    echo "✅ Pranota found: {$pranota->nomor_pranota}\n\n";
    
    echo "=== NIK Preview for Print ===\n";
    foreach ($pranota->uangJalans as $index => $uangJalan) {
        $no = $index + 1;
        echo "Item {$no}: {$uangJalan->nomor_uang_jalan}\n";
        
        if ($uangJalan->suratJalan) {
            $sj = $uangJalan->suratJalan;
            echo "- Surat Jalan: {$sj->no_surat_jalan}\n";
            echo "- Supir: " . ($sj->supir ?: 'NULL') . "\n";
            echo "- Kenek: " . ($sj->kenek ?: 'NULL') . "\n";
            
            // Test the NIK logic from print template
            if ($sj->supirKaryawan) {
                echo "- ✅ NIK (Supir): {$sj->supirKaryawan->nik}\n";
            } elseif ($sj->kenekKaryawan) {
                echo "- ✅ NIK (Kenek): {$sj->kenekKaryawan->nik}\n";
            } else {
                echo "- ❌ NIK: -\n";
            }
        }
        echo "\n";
    }
}

echo "3. Testing alternative approach - find by nama_lengkap...\n";
// Sometimes names might match nama_lengkap instead of nama_panggilan
$suratJalan2 = \App\Models\SuratJalan::whereNotNull('supir')->first();
if ($suratJalan2) {
    $supirByNamaLengkap = \App\Models\Karyawan::where('nama_lengkap', $suratJalan2->supir)->first();
    if ($supirByNamaLengkap) {
        echo "✅ Found supir by nama_lengkap: {$supirByNamaLengkap->nik} - {$supirByNamaLengkap->nama_lengkap}\n";
    } else {
        echo "❌ No match by nama_lengkap for: {$suratJalan2->supir}\n";
    }
}

echo "\n✅ Test completed! Check print page to see if NIK shows up now.\n";