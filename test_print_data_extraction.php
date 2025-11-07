<?php

require_once 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING PRINT VIEW DATA EXTRACTION ===\n\n";

try {
    // Get a sample surat jalan with relationships
    $suratJalan = \App\Models\SuratJalan::with(['order', 'order.jenisBarang'])->first();
    
    if ($suratJalan) {
        echo "TESTING DATA THAT WILL APPEAR IN PRINT:\n\n";
        
        // Test tanggal
        $tanggal = \Carbon\Carbon::parse($suratJalan->tanggal_surat_jalan ?? now())->format('d-M-Y');
        echo "Tanggal: $tanggal\n";
        
        // Test nomor surat jalan
        $noSuratJalan = $suratJalan->no_surat_jalan ?? 'SJ-' . date('mdY');
        echo "Nomor Surat Jalan: $noSuratJalan\n";
        
        // Test no plat
        $noPlat = strtoupper($suratJalan->no_plat ?? ($suratJalan->no_plat != '--Pilih No Plat' ? $suratJalan->no_plat : ''));
        echo "No Plat: $noPlat\n";
        
        // Test supir
        $supir = strtoupper($suratJalan->supir ?? 'SUMANTA');
        echo "Supir: $supir\n";
        
        // Test tujuan pengiriman
        $tujuanKirim = strtoupper($suratJalan->tujuan_pengiriman ?? 'SUKABUMI');
        echo "Tujuan Kirim: $tujuanKirim\n";
        
        // Test tujuan pengambilan
        $tujuanAmbil = strtoupper($suratJalan->tujuan_pengambilan ?? 'BATAM');
        echo "Tujuan Ambil: $tujuanAmbil\n";
        
        // Test jenis barang (dengan null safe)
        $jenisBarang = strtoupper($suratJalan->jenis_barang ?? ($suratJalan->order && $suratJalan->order->jenisBarang ? $suratJalan->order->jenisBarang->nama : 'AQUA'));
        echo "Jenis Barang: $jenisBarang\n";
        
        // Test pengirim
        $pengirim = strtoupper($suratJalan->pengirim ?? 'PT TIRTA INVESTAMA');
        echo "Pengirim: $pengirim\n";
        
        // Test no kontainer
        $noKontainer = strtoupper($suratJalan->no_kontainer ?? '');
        echo "No Kontainer: $noKontainer\n";
        
        // Test no seal
        $noSeal = $suratJalan->no_seal ? strtoupper($suratJalan->no_seal) : '';
        echo "No Seal: $noSeal\n";
        
        // Test tipe kontainer
        $tipeKontainer = strtoupper($suratJalan->tipe_kontainer ?? 'FCL');
        echo "Tipe Kontainer: $tipeKontainer\n";
        
    } else {
        echo "No surat jalan found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";