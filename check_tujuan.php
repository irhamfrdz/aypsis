<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SuratJalan;
use App\Models\TujuanKegiatanUtama;

echo "=== Checking specific tujuan values ===\n\n";

$tujuans = ['SUKABUMI', 'CIKARANG UJUNG', 'CIBITUNG', 'SENTUL', 'CIKARANG'];
foreach($tujuans as $t) { 
    $found = TujuanKegiatanUtama::where('ke', $t)->first(); 
    if($found) { 
        echo $t . ': FOUND - ongkos_20ft=' . $found->ongkos_truk_20ft . "\n"; 
    } else { 
        echo $t . ': NOT FOUND' . "\n"; 
    } 
}

echo "\n=== Test relation with SUKABUMI ===\n";
$sjSukabumi = SuratJalan::with('tujuanPengambilanRelation')
    ->where('tujuan_pengambilan', 'SUKABUMI')
    ->first();
    
if ($sjSukabumi) {
    echo "Found SJ with tujuan_pengambilan = SUKABUMI\n";
    if ($sjSukabumi->tujuanPengambilanRelation) {
        echo "Relation: " . $sjSukabumi->tujuanPengambilanRelation->ke . "\n";
        echo "Ongkos 20ft: " . $sjSukabumi->tujuanPengambilanRelation->ongkos_truk_20ft . "\n";
    } else {
        echo "Relation NOT found\n";
    }
}
