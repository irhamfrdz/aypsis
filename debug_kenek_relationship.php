<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debug Kenek Relationship ===\n\n";

$suratJalan = \App\Models\SuratJalan::whereNotNull('kenek')->first();
if ($suratJalan) {
    echo "SuratJalan kenek: '{$suratJalan->kenek}'\n";
    
    // Try different matching approaches
    echo "1. Matching with nama_panggilan:\n";
    $karyawan1 = \App\Models\Karyawan::where('nama_panggilan', $suratJalan->kenek)->first();
    if ($karyawan1) {
        echo "✅ Found: {$karyawan1->nik} - {$karyawan1->nama_lengkap} ({$karyawan1->nama_panggilan})\n";
    } else {
        echo "❌ No match\n";
    }
    
    echo "2. Matching with nama_lengkap:\n";
    $karyawan2 = \App\Models\Karyawan::where('nama_lengkap', $suratJalan->kenek)->first();
    if ($karyawan2) {
        echo "✅ Found: {$karyawan2->nik} - {$karyawan2->nama_lengkap} ({$karyawan2->nama_panggilan})\n";
    } else {
        echo "❌ No match\n";
    }
    
    echo "3. Partial matching (LIKE):\n";
    $karyawan3 = \App\Models\Karyawan::where('nama_panggilan', 'like', "%{$suratJalan->kenek}%")
        ->orWhere('nama_lengkap', 'like', "%{$suratJalan->kenek}%")
        ->first();
    if ($karyawan3) {
        echo "✅ Found: {$karyawan3->nik} - {$karyawan3->nama_lengkap} ({$karyawan3->nama_panggilan})\n";
    } else {
        echo "❌ No match\n";
    }
    
    echo "\n4. All karyawan with similar names:\n";
    $similarKaryawan = \App\Models\Karyawan::where('nama_panggilan', 'like', "%ADR%")
        ->orWhere('nama_lengkap', 'like', "%ADR%")
        ->get(['nik', 'nama_lengkap', 'nama_panggilan']);
    
    foreach ($similarKaryawan as $k) {
        echo "- {$k->nik}: {$k->nama_lengkap} ({$k->nama_panggilan})\n";
    }
}