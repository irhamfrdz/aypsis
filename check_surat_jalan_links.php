<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PranotaSuratJalan;
use App\Models\SuratJalan;

// Check if there's any surat jalan with pranota_surat_jalan_id = 12
$suratJalans = SuratJalan::where('pranota_surat_jalan_id', 12)->get();
echo "Surat jalans linked to pranota 12: " . $suratJalans->count() . "\n";

// If no links found, let's check recent surat jalans created around the same time as this pranota
$pranota = PranotaSuratJalan::find(12);
if ($pranota) {
    echo "Pranota created at: " . $pranota->created_at . "\n";
    
    // Find surat jalans created around the same time (within 1 hour)
    $startTime = $pranota->created_at->copy()->subHour();
    $endTime = $pranota->created_at->copy()->addHour();
    
    $recentSJ = SuratJalan::whereBetween('created_at', [$startTime, $endTime])->get();
    
    echo "Surat jalans created around the same time: " . $recentSJ->count() . "\n";
    foreach ($recentSJ as $sj) {
        echo "- " . $sj->no_surat_jalan . " (ID: " . $sj->id . ") - Created: " . $sj->created_at . "\n";
    }
    
    // Also check if there are any unlinked surat jalans that we can link
    echo "\nChecking unlinked surat jalans:\n";
    $unlinkedSJ = SuratJalan::whereNull('pranota_surat_jalan_id')->orderBy('created_at', 'desc')->take(5)->get();
    foreach ($unlinkedSJ as $sj) {
        echo "- " . $sj->no_surat_jalan . " (ID: " . $sj->id . ") - Created: " . $sj->created_at . "\n";
    }
}