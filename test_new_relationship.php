<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PranotaSuratJalan;
use App\Models\SuratJalan;

$pranota = PranotaSuratJalan::where('nomor_pranota', 'PSJ-1125-000015')->first();

if ($pranota) {
    echo "Pranota found: " . $pranota->nomor_pranota . "\n";
    
    echo "\nTesting new relationship:\n";
    $suratJalans = $pranota->suratJalans;
    echo "- Count: " . $suratJalans->count() . "\n";
    
    echo "\nTesting accessor:\n";
    $firstSJ = $pranota->surat_jalan;
    if ($firstSJ) {
        echo "- Found via accessor: " . $firstSJ->nomor_surat_jalan . "\n";
        echo "- Supir: " . ($firstSJ->supir ?? '-') . "\n";
        echo "- Tujuan: " . ($firstSJ->tujuan->nama_tujuan ?? '-') . "\n";
    } else {
        echo "- No surat jalan found via accessor\n";
    }
    
} else {
    echo "Pranota not found!\n";
}