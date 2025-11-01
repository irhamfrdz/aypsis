<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TandaTerima;
use App\Models\Prospek;
use App\Models\Bl;

echo "=== DETAIL CHECK TERM DATA ===\n\n";

// Check tanda terima with term data
echo "1. TandaTerima yang memiliki data term:\n";
$tandaTerimasWithTerm = TandaTerima::whereNotNull('term')->get();
foreach ($tandaTerimasWithTerm as $tt) {
    echo "TandaTerima ID: {$tt->id}\n";
    echo "Term: {$tt->term}\n";
    echo "No Surat Jalan: {$tt->no_surat_jalan}\n";
    echo "---\n";
}

if ($tandaTerimasWithTerm->isEmpty()) {
    echo "Tidak ada tanda terima dengan data term.\n";
}

// Check prospek yang menggunakan tanda terima dengan term
echo "\n2. Prospek yang terkait dengan TandaTerima berterm:\n";
if (!$tandaTerimasWithTerm->isEmpty()) {
    $ttIds = $tandaTerimasWithTerm->pluck('id');
    $prospeksWithTerm = Prospek::with('tandaTerima')->whereIn('tanda_terima_id', $ttIds)->get();
    
    foreach ($prospeksWithTerm as $prospek) {
        echo "Prospek ID: {$prospek->id}\n";
        echo "Tanda Terima ID: {$prospek->tanda_terima_id}\n";
        echo "Term: " . ($prospek->tandaTerima ? $prospek->tandaTerima->term : 'NULL') . "\n";
        echo "---\n";
    }
    
    if ($prospeksWithTerm->isEmpty()) {
        echo "Tidak ada prospek yang menggunakan tanda terima dengan term.\n";
    }
}

// Check BL dari prospek tersebut
echo "\n3. BL yang dibuat dari prospek dengan term:\n";
if (!empty($prospeksWithTerm)) {
    $prospekIds = $prospeksWithTerm->pluck('id');
    $blsFromTermProspek = Bl::whereIn('prospek_id', $prospekIds)->get();
    
    foreach ($blsFromTermProspek as $bl) {
        echo "BL ID: {$bl->id}\n";
        echo "Prospek ID: {$bl->prospek_id}\n";
        echo "Term: " . ($bl->term ?? 'NULL') . "\n";
        echo "Volume: " . ($bl->volume ?? 'NULL') . "\n";
        echo "---\n";
    }
    
    if ($blsFromTermProspek->isEmpty()) {
        echo "Belum ada BL yang dibuat dari prospek dengan term.\n";
    }
}

echo "\n=== END DEBUG ===\n";