<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Final NIK Implementation ===\n\n";

// Test the accessors
echo "1. Testing supir_nik and kenek_nik accessors...\n";
$suratJalan = \App\Models\SuratJalan::whereNotNull('supir')->first();
if ($suratJalan) {
    echo "SuratJalan: {$suratJalan->no_surat_jalan}\n";
    echo "Supir: {$suratJalan->supir}\n";
    echo "Kenek: " . ($suratJalan->kenek ?: 'NULL') . "\n";
    echo "Supir NIK: " . ($suratJalan->supir_nik ?: 'NULL') . "\n";
    echo "Kenek NIK: " . ($suratJalan->kenek_nik ?: 'NULL') . "\n";
}

echo "\n2. Testing PranotaUangJalan NIK display...\n";
$pranota = \App\Models\PranotaUangJalan::with(['uangJalans.suratJalan', 'creator'])->first();

if ($pranota) {
    echo "✅ Pranota: {$pranota->nomor_pranota}\n\n";
    
    echo "=== Final NIK Preview ===\n";
    printf("%-3s | %-15s | %-15s | %-10s\n", "No", "Surat Jalan", "Supir", "NIK");
    echo str_repeat('-', 50) . "\n";
    
    foreach ($pranota->uangJalans as $index => $uangJalan) {
        $no = $index + 1;
        $noSuratJalan = $uangJalan->suratJalan ? $uangJalan->suratJalan->no_surat_jalan : '-';
        $supir = $uangJalan->suratJalan ? ($uangJalan->suratJalan->supir ?: '-') : '-';
        
        // Test NIK logic dari print template
        $nik = '-';
        if ($uangJalan->suratJalan) {
            if ($uangJalan->suratJalan->supir_nik) {
                $nik = $uangJalan->suratJalan->supir_nik;
            } elseif ($uangJalan->suratJalan->kenek_nik) {
                $nik = $uangJalan->suratJalan->kenek_nik;
            }
        }
        
        printf("%-3s | %-15s | %-15s | %-10s\n", 
            $no, 
            substr($noSuratJalan, 0, 15),
            substr($supir, 0, 15),
            $nik
        );
    }
}

echo "\n3. Testing individual cases...\n";
$testCases = \App\Models\SuratJalan::whereNotNull('supir')
    ->orWhereNotNull('kenek')
    ->limit(3)
    ->get();

foreach ($testCases as $sj) {
    echo "- {$sj->no_surat_jalan}: ";
    echo "Supir='{$sj->supir}' (NIK: " . ($sj->supir_nik ?: 'NULL') . "), ";
    echo "Kenek='" . ($sj->kenek ?: 'NULL') . "' (NIK: " . ($sj->kenek_nik ?: 'NULL') . ")\n";
}

echo "\n✅ FINAL TEST COMPLETED!\n";
echo "💡 NIK sekarang akan tampil di kolom NIK pada halaman print.\n";
echo "🖨️ Test print: /pranota-uang-jalan/{$pranota->id}/print\n";