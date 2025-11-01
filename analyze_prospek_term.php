<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Prospek;
use App\Models\TandaTerima;

echo "=== ANALISIS PROSPEK DAN TANDA TERIMA ===\n\n";

// Cek prospek terbaru
echo "1. Prospek terbaru (10 terakhir):\n";
$latestProspeks = Prospek::with('tandaTerima')->orderBy('id', 'desc')->take(10)->get();
foreach ($latestProspeks as $prospek) {
    echo "Prospek ID: {$prospek->id}\n";
    echo "Tanda Terima ID: " . ($prospek->tanda_terima_id ?? 'NULL') . "\n";
    if ($prospek->tandaTerima) {
        echo "Term dari TandaTerima: " . ($prospek->tandaTerima->term ?? 'NULL') . "\n";
    }
    echo "Status: {$prospek->status}\n";
    echo "Tujuan: {$prospek->tujuan_pengiriman}\n";
    echo "---\n";
}

// Cek tanda terima terbaru
echo "\n2. TandaTerima terbaru (5 terakhir):\n";
$latestTandaTerimas = TandaTerima::orderBy('id', 'desc')->take(5)->get();
foreach ($latestTandaTerimas as $tt) {
    echo "TandaTerima ID: {$tt->id}\n";
    echo "Term: " . ($tt->term ?? 'NULL') . "\n";
    echo "No Surat Jalan: {$tt->no_surat_jalan}\n";
    echo "---\n";
}

echo "\n=== SOLUSI ===\n";
echo "Untuk mengatasi masalah ini:\n";
echo "1. Pastikan prospek yang akan diproses naik kapal memiliki tanda_terima_id\n";
echo "2. Pastikan tanda terima tersebut memiliki data term\n";
echo "3. Atau buat prospek baru yang terhubung dengan tanda terima yang berterm\n";

echo "\n=== END ANALISIS ===\n";