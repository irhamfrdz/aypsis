<?php

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProspekKapal;
use App\Models\TandaTerima;
use App\Models\TandaTerimaTanpaSuratJalan;

try {
    echo "=== DEBUG: Prospek Kapal Container Availability ===\n\n";
    
    // Get all prospek kapal
    $prospekKapals = ProspekKapal::all();
    
    if ($prospekKapals->count() == 0) {
        echo "No prospek kapal found in database.\n";
        exit(0);
    }
    
    echo "Found " . $prospekKapals->count() . " prospek kapal(s):\n";
    foreach ($prospekKapals as $pk) {
        echo "- ID: {$pk->id}, Voyage: {$pk->voyage}, Kapal: {$pk->nama_kapal}\n";
    }
    
    echo "\n=== Checking Tanda Terima Availability ===\n";
    
    // For each prospek kapal, check available tanda terima
    foreach ($prospekKapals as $pk) {
        echo "\n--- PROSPEK KAPAL: {$pk->voyage} ({$pk->nama_kapal}) ---\n";
        
        // Check all approved tanda terima
        $allApprovedTT = TandaTerima::where('status', 'approved')->get();
        echo "Total approved Tanda Terima: " . $allApprovedTT->count() . "\n";
        
        // Check which ones match the vessel name
        $matchingTT = TandaTerima::where('status', 'approved')
            ->where('estimasi_nama_kapal', $pk->nama_kapal)
            ->get();
        echo "Matching vessel name '{$pk->nama_kapal}': " . $matchingTT->count() . "\n";
        
        if ($matchingTT->count() > 0) {
            foreach ($matchingTT as $tt) {
                echo "  - TT ID: {$tt->id}, No: {$tt->no_surat_jalan}, Kapal: {$tt->estimasi_nama_kapal}\n";
            }
        }
        
        // Check which ones are already used in this prospek kapal
        $usedTT = TandaTerima::where('status', 'approved')
            ->where('estimasi_nama_kapal', $pk->nama_kapal)
            ->whereIn('id', function($query) use ($pk) {
                $query->select('tanda_terima_id')
                      ->from('prospek_kapal_kontainers')
                      ->where('prospek_kapal_id', $pk->id)
                      ->whereNotNull('tanda_terima_id');
            })
            ->get();
        echo "Already used in this prospek kapal: " . $usedTT->count() . "\n";
        
        // Final available count
        $availableTT = TandaTerima::where('status', 'approved')
            ->where('estimasi_nama_kapal', $pk->nama_kapal)
            ->whereNotIn('id', function($query) use ($pk) {
                $query->select('tanda_terima_id')
                      ->from('prospek_kapal_kontainers')
                      ->where('prospek_kapal_id', $pk->id)
                      ->whereNotNull('tanda_terima_id');
            })
            ->get();
        echo "AVAILABLE for adding: " . $availableTT->count() . "\n";
        
        if ($availableTT->count() > 0) {
            foreach ($availableTT as $tt) {
                echo "  ✅ Available - TT ID: {$tt->id}, No: {$tt->no_surat_jalan}\n";
            }
        }
    }
    
    echo "\n=== Summary: All Tanda Terima by Vessel ===\n";
    $ttByVessel = TandaTerima::where('status', 'approved')
        ->whereNotNull('estimasi_nama_kapal')
        ->get()
        ->groupBy('estimasi_nama_kapal');
    
    foreach ($ttByVessel as $vessel => $tts) {
        echo "Vessel '{$vessel}': " . $tts->count() . " tanda terima\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}