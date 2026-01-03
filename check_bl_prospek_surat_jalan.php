<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Bl;
use App\Models\Prospek;
use App\Models\SuratJalan;

echo "=== Checking BL -> Prospek -> Surat Jalan Relationship ===\n\n";

// Get first BL with prospek
$bl = Bl::with(['prospek.suratJalan'])->whereNotNull('prospek_id')->first();

if (!$bl) {
    echo "No BL found with prospek_id\n";
    exit;
}

echo "BL Information:\n";
echo "- BL ID: {$bl->id}\n";
echo "- Nomor BL: " . ($bl->nomor_bl ?: '-') . "\n";
echo "- Prospek ID: " . ($bl->prospek_id ?: 'NULL') . "\n\n";

if ($bl->prospek) {
    echo "Prospek Information:\n";
    echo "- Prospek ID: {$bl->prospek->id}\n";
    echo "- No Surat Jalan (field): " . ($bl->prospek->no_surat_jalan ?: 'NULL') . "\n";
    echo "- Surat Jalan ID (FK): " . ($bl->prospek->surat_jalan_id ?: 'NULL') . "\n\n";
    
    if ($bl->prospek->suratJalan) {
        echo "Surat Jalan (from relation):\n";
        echo "- Surat Jalan ID: {$bl->prospek->suratJalan->id}\n";
        echo "- No Surat Jalan: {$bl->prospek->suratJalan->no_surat_jalan}\n\n";
    } else {
        echo "No Surat Jalan relation found\n\n";
        
        // Check if there's a surat jalan with matching no_surat_jalan
        if ($bl->prospek->no_surat_jalan) {
            $sj = SuratJalan::where('no_surat_jalan', $bl->prospek->no_surat_jalan)->first();
            if ($sj) {
                echo "⚠️ Found Surat Jalan by no_surat_jalan but not linked via FK:\n";
                echo "- Surat Jalan ID: {$sj->id}\n";
                echo "- No Surat Jalan: {$sj->no_surat_jalan}\n";
                echo "- Prospek surat_jalan_id should be: {$sj->id}\n\n";
            }
        }
    }
} else {
    echo "No Prospek found for this BL\n";
}

// Check statistics
echo "\n=== Statistics ===\n";
echo "Total BL: " . Bl::count() . "\n";
echo "BL with prospek_id: " . Bl::whereNotNull('prospek_id')->count() . "\n";
echo "Total Prospek: " . Prospek::count() . "\n";
echo "Prospek with surat_jalan_id: " . Prospek::whereNotNull('surat_jalan_id')->count() . "\n";
echo "Prospek with no_surat_jalan: " . Prospek::whereNotNull('no_surat_jalan')->count() . "\n";
echo "Total Surat Jalan: " . SuratJalan::count() . "\n";
