<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test NIK Field After Fix ===\n\n";

// Test relationship yang baru
echo "1. Testing karyawanRelation...\n";
$suratJalan = \App\Models\SuratJalan::with('karyawanRelation')->first();
if ($suratJalan) {
    echo "SuratJalan ID: {$suratJalan->id}\n";
    echo "Karyawan field value: " . ($suratJalan->karyawan ?: 'NULL') . "\n";
    
    if ($suratJalan->karyawanRelation) {
        echo "✅ KaryawanRelation found:\n";
        echo "- ID: {$suratJalan->karyawanRelation->id}\n";
        echo "- NIK: {$suratJalan->karyawanRelation->nik}\n";
        echo "- Nama: " . ($suratJalan->karyawanRelation->nama_lengkap ?? $suratJalan->karyawanRelation->nama_panggilan) . "\n";
    } else {
        echo "❌ No karyawanRelation found\n";
        
        // Check if karyawan field is numeric (ID)
        if (is_numeric($suratJalan->karyawan)) {
            echo "🔍 Karyawan field contains ID: {$suratJalan->karyawan}\n";
            $karyawan = \App\Models\Karyawan::find($suratJalan->karyawan);
            if ($karyawan) {
                echo "✅ Found karyawan by ID: {$karyawan->nik} - {$karyawan->nama_lengkap}\n";
            } else {
                echo "❌ No karyawan found with ID: {$suratJalan->karyawan}\n";
            }
        }
    }
}

echo "\n2. Testing PranotaUangJalan with relationships...\n";
$pranota = \App\Models\PranotaUangJalan::with(['uangJalans.suratJalan.karyawanRelation', 'creator'])
    ->first();

if ($pranota) {
    echo "✅ Pranota found: {$pranota->nomor_pranota}\n";
    echo "Jumlah Uang Jalan: {$pranota->uangJalans->count()}\n\n";
    
    echo "=== Preview NIK Data ===\n";
    foreach ($pranota->uangJalans as $index => $uangJalan) {
        $no = $index + 1;
        echo "Item {$no}:\n";
        echo "- Nomor Uang Jalan: {$uangJalan->nomor_uang_jalan}\n";
        echo "- Surat Jalan: " . ($uangJalan->suratJalan ? $uangJalan->suratJalan->no_surat_jalan : 'N/A') . "\n";
        
        if ($uangJalan->suratJalan) {
            $sj = $uangJalan->suratJalan;
            echo "- Karyawan field: " . ($sj->karyawan ?: 'NULL') . "\n";
            
            if ($sj->karyawanRelation) {
                echo "- ✅ NIK (from relation): {$sj->karyawanRelation->nik}\n";
                echo "- Nama: " . ($sj->karyawanRelation->nama_lengkap ?? $sj->karyawanRelation->nama_panggilan) . "\n";
            } else {
                echo "- ❌ No karyawanRelation\n";
                
                // Fallback: try to get by ID
                if (is_numeric($sj->karyawan)) {
                    $karyawan = \App\Models\Karyawan::find($sj->karyawan);
                    if ($karyawan) {
                        echo "- 🔧 NIK (fallback): {$karyawan->nik}\n";
                    }
                }
            }
        }
        echo "\n";
    }
}

echo "\n3. Checking database karyawan field values...\n";
$suratJalans = \App\Models\SuratJalan::whereNotNull('karyawan')
    ->limit(5)
    ->get(['id', 'no_surat_jalan', 'karyawan']);

foreach ($suratJalans as $sj) {
    echo "- SJ {$sj->no_surat_jalan}: karyawan = {$sj->karyawan}\n";
    
    if (is_numeric($sj->karyawan)) {
        $karyawan = \App\Models\Karyawan::find($sj->karyawan);
        if ($karyawan) {
            echo "  → Karyawan: {$karyawan->nik} - {$karyawan->nama_lengkap}\n";
        }
    }
}

echo "\n✅ Test selesai! NIK seharusnya sudah tampil di halaman print.\n";