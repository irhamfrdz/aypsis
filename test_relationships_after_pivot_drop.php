<?php

require_once 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING RELATIONSHIPS AFTER PIVOT TABLE REMOVAL ===\n\n";

try {
    // Test pranota with surat_jalan_id
    $pranotaWithSuratJalanId = \App\Models\PranotaSuratJalan::whereNotNull('surat_jalan_id')->first();
    
    if ($pranotaWithSuratJalanId) {
        echo "PRANOTA WITH SURAT_JALAN_ID:\n";
        echo "Pranota: {$pranotaWithSuratJalanId->nomor_pranota}\n";
        echo "Surat Jalan ID: {$pranotaWithSuratJalanId->surat_jalan_id}\n";
        
        // Test direct relationship
        $directSuratJalan = $pranotaWithSuratJalanId->suratJalan;
        if ($directSuratJalan) {
            echo "Direct relationship works: {$directSuratJalan->nomor_surat_jalan}\n";
        } else {
            echo "Direct relationship: NULL\n";
        }
        
        // Test accessor
        $accessorSuratJalan = $pranotaWithSuratJalanId->getSuratJalanAttribute();
        if ($accessorSuratJalan) {
            echo "Accessor works: {$accessorSuratJalan->nomor_surat_jalan}\n";
        } else {
            echo "Accessor: NULL\n";
        }
    } else {
        echo "No pranota with surat_jalan_id found\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
    
    // Test pranota with hasMany relationship
    $pranotaWithSuratJalans = \App\Models\PranotaSuratJalan::has('suratJalans')->first();
    
    if ($pranotaWithSuratJalans) {
        echo "PRANOTA WITH HASMANY RELATIONSHIP:\n";
        echo "Pranota: {$pranotaWithSuratJalans->nomor_pranota}\n";
        
        $suratJalansCount = $pranotaWithSuratJalans->suratJalans->count();
        echo "Surat Jalans count: {$suratJalansCount}\n";
        
        if ($suratJalansCount > 0) {
            $firstSuratJalan = $pranotaWithSuratJalans->suratJalans->first();
            echo "First Surat Jalan: {$firstSuratJalan->nomor_surat_jalan}\n";
        }
        
        // Test backward compatibility method
        $firstViaMethpd = $pranotaWithSuratJalans->getFirstSuratJalan();
        if ($firstViaMethpd) {
            echo "getFirstSuratJalan(): {$firstViaMethpd->nomor_surat_jalan}\n";
        }
    } else {
        echo "No pranota with hasMany suratJalans found\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
    
    // Summary statistics
    echo "SUMMARY:\n";
    $totalPranota = \App\Models\PranotaSuratJalan::count();
    $pranotaWithSuratJalanIdCount = \App\Models\PranotaSuratJalan::whereNotNull('surat_jalan_id')->count();
    $pranotaWithSuratJalansCount = \App\Models\PranotaSuratJalan::has('suratJalans')->count();
    
    echo "Total Pranota: {$totalPranota}\n";
    echo "Pranota with surat_jalan_id: {$pranotaWithSuratJalanIdCount}\n";
    echo "Pranota with hasMany suratJalans: {$pranotaWithSuratJalansCount}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";