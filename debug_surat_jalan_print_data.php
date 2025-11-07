<?php

require_once 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING SURAT JALAN DATA FOR PRINT ===\n\n";

try {
    // Get a sample surat jalan
    $suratJalan = \App\Models\SuratJalan::first();
    
    if ($suratJalan) {
        echo "SURAT JALAN DATA:\n";
        echo "ID: {$suratJalan->id}\n";
        echo "Nomor Surat Jalan: " . ($suratJalan->nomor_surat_jalan ?? 'NULL') . "\n";
        echo "Supir: " . ($suratJalan->supir ?? 'NULL') . "\n";
        echo "No Plat: " . ($suratJalan->no_plat ?? 'NULL') . "\n";
        echo "Tujuan Pengiriman: " . ($suratJalan->tujuan_pengiriman ?? 'NULL') . "\n";
        echo "Tujuan Pengambilan: " . ($suratJalan->tujuan_pengambilan ?? 'NULL') . "\n";
        echo "No Kontainer: " . ($suratJalan->no_kontainer ?? 'NULL') . "\n";
        echo "No Seal: " . ($suratJalan->no_seal ?? 'NULL') . "\n";
        echo "Jenis Barang: " . ($suratJalan->jenis_barang ?? 'NULL') . "\n";
        echo "Pengirim: " . ($suratJalan->pengirim ?? 'NULL') . "\n";
        echo "Tipe Kontainer: " . ($suratJalan->tipe_kontainer ?? 'NULL') . "\n";
        
        echo "\nALL ATTRIBUTES:\n";
        foreach ($suratJalan->getAttributes() as $key => $value) {
            echo "$key: " . ($value ?? 'NULL') . "\n";
        }
        
        // Check relationships
        echo "\nRELATIONSHIPS:\n";
        if ($suratJalan->order) {
            echo "Order exists: YES\n";
            echo "Order ID: {$suratJalan->order->id}\n";
            if ($suratJalan->order->jenisBarang) {
                echo "Jenis Barang from Order: {$suratJalan->order->jenisBarang->nama}\n";
            }
        } else {
            echo "Order exists: NO\n";
        }
        
    } else {
        echo "No surat jalan found in database\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";