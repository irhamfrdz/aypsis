<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalan;

// Link pranota 12 with surat jalan SJT30 (ID: 44)
$suratJalan = SuratJalan::find(44);
if ($suratJalan) {
    $suratJalan->update(['pranota_surat_jalan_id' => 12]);
    echo "Successfully linked surat jalan " . $suratJalan->no_surat_jalan . " with pranota 12\n";
    
    // Check if it has tujuan relationship
    $tujuan = $suratJalan->tujuan;
    if ($tujuan) {
        echo "Tujuan: " . $tujuan->nama_tujuan . "\n";
    } else {
        echo "No tujuan linked\n";
    }
    
    echo "Supir: " . ($suratJalan->supir ?? '-') . "\n";
} else {
    echo "Surat jalan not found\n";
}