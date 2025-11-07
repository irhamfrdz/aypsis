<?php

require_once 'bootstrap/app.php';
$app = $app ?? Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))->create();
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUG OB MUAT DATA ===" . PHP_EOL;
echo PHP_EOL;

// Check Master Kapal
echo "1. MASTER KAPAL (AKTIF):" . PHP_EOL;
$masterKapals = \App\Models\MasterKapal::where('status', 'aktif')->orderBy('nama_kapal')->get();
echo "Total: " . $masterKapals->count() . PHP_EOL;
foreach($masterKapals as $kapal) {
    echo "- " . $kapal->nama_kapal . " (Status: " . $kapal->status . ")" . PHP_EOL;
}

echo PHP_EOL;

// Check Pergerakan Kapal
echo "2. PERGERAKAN KAPAL (AKTIF):" . PHP_EOL;
$pergerakanKapals = \App\Models\PergerakanKapal::whereIn('status', ['sandar', 'labuh', 'loading', 'discharging'])
                                               ->orderBy('nama_kapal')
                                               ->orderBy('voyage')
                                               ->get();
echo "Total: " . $pergerakanKapals->count() . PHP_EOL;
foreach($pergerakanKapals as $voyage) {
    echo "- " . $voyage->nama_kapal . " | " . $voyage->voyage . " | " . $voyage->status . PHP_EOL;
}

echo PHP_EOL;

// Check grouping
echo "3. GROUPING BY NAMA KAPAL:" . PHP_EOL;
$grouped = $pergerakanKapals->groupBy('nama_kapal');
foreach($grouped as $namaKapal => $voyages) {
    echo "Kapal: " . $namaKapal . " (" . $voyages->count() . " voyages)" . PHP_EOL;
    foreach($voyages as $voyage) {
        echo "  - " . $voyage->voyage . " (" . $voyage->status . ")" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== END DEBUG ===" . PHP_EOL;

?>