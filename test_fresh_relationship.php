<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PranotaSuratJalan;
use App\Models\SuratJalan;

echo "Testing fresh relationships:\n";

$pranota = PranotaSuratJalan::with(['suratJalans.tujuan', 'suratJalans.karyawan'])->where('nomor_pranota', 'PSJ-1125-000015')->first();

if ($pranota) {
    echo "Pranota found: " . $pranota->nomor_pranota . "\n";
    
    $suratJalans = $pranota->suratJalans;
    echo "Count: " . $suratJalans->count() . "\n";
    
    if ($suratJalans->count() > 0) {
        $firstSJ = $suratJalans->first();
        echo "First SJ: " . $firstSJ->no_surat_jalan . "\n";
        echo "Supir: " . ($firstSJ->supir ?? '-') . "\n";
        
        if ($firstSJ->tujuan) {
            echo "Tujuan: " . $firstSJ->tujuan->nama_tujuan . "\n";
        } else {
            echo "No tujuan\n";
        }
    }
    
    // Test accessor
    $suratJalan = $pranota->surat_jalan;
    if ($suratJalan) {
        echo "\nVia accessor:\n";
        echo "- Nomor: " . $suratJalan->no_surat_jalan . "\n";
        echo "- Supir: " . ($suratJalan->supir ?? '-') . "\n";
    } else {
        echo "No surat jalan via accessor\n";
    }
}