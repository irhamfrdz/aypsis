<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PranotaSuratJalan;
use Illuminate\Support\Facades\DB;

$pranota = PranotaSuratJalan::where('nomor_pranota', 'PSJ-1125-000015')->first();

if ($pranota) {
    echo "Pranota found:\n";
    echo "- ID: " . $pranota->id . "\n";
    echo "- Nomor: " . $pranota->nomor_pranota . "\n";
    echo "- Total Amount: " . $pranota->total_amount . "\n";
    
    echo "\nChecking suratJalans relationship:\n";
    $suratJalans = $pranota->suratJalans;
    echo "- Count: " . $suratJalans->count() . "\n";
    
    echo "\nChecking pivot table directly:\n";
    $items = DB::table('pranota_surat_jalan_items')->where('pranota_surat_jalan_id', $pranota->id)->get();
    echo "- Items in pivot table: " . $items->count() . "\n";
    
    foreach ($items as $item) {
        echo "  - Surat Jalan ID: " . $item->surat_jalan_id . "\n";
        
        // Get surat jalan details
        $suratJalan = DB::table('surat_jalans')->where('id', $item->surat_jalan_id)->first();
        if ($suratJalan) {
            echo "    - Nomor SJ: " . $suratJalan->nomor_surat_jalan . "\n";
            echo "    - Supir: " . $suratJalan->supir . "\n";
            echo "    - Tujuan ID: " . $suratJalan->tujuan_id . "\n";
        }
    }
    
    echo "\nTesting accessor:\n";
    $firstSJ = $pranota->surat_jalan;
    if ($firstSJ) {
        echo "- First SJ via accessor: " . $firstSJ->nomor_surat_jalan . "\n";
    } else {
        echo "- No surat jalan found via accessor\n";
    }
    
} else {
    echo "Pranota not found!\n";
}