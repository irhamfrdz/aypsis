<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;
use App\Models\TandaTerima;
use Illuminate\Support\Facades\DB;

echo "=== AUTO-LINKING PROSPEK TO TANDA TERIMA BY SURAT JALAN ID ===\n\n";

// Method 1: Link by surat_jalan_id (Primary key matching)
echo "1. LINKING BY SURAT_JALAN_ID:\n";

$prospekWithoutTandaTerima = Prospek::whereNull('tanda_terima_id')
    ->whereNotNull('surat_jalan_id')
    ->get();

echo "Found " . $prospekWithoutTandaTerima->count() . " prospek records without tanda_terima_id but with surat_jalan_id\n\n";

$linkedCount = 0;
$errors = [];

foreach ($prospekWithoutTandaTerima as $prospek) {
    // Find TandaTerima with matching surat_jalan_id
    $tandaTerima = TandaTerima::where('surat_jalan_id', $prospek->surat_jalan_id)->first();
    
    if ($tandaTerima) {
        echo "Linking Prospek ID {$prospek->id} (surat_jalan_id: {$prospek->surat_jalan_id}) to TandaTerima ID {$tandaTerima->id}";
        if ($tandaTerima->term) {
            echo " (term: {$tandaTerima->term})";
        }
        echo "\n";
        
        // Update prospek with tanda_terima_id
        try {
            $prospek->tanda_terima_id = $tandaTerima->id;
            $prospek->save();
            $linkedCount++;
        } catch (Exception $e) {
            $errors[] = "Failed to link Prospek ID {$prospek->id}: " . $e->getMessage();
            echo "  ERROR: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nMethod 1 Results: Successfully linked {$linkedCount} prospek records by surat_jalan_id\n\n";

// Method 2: Link by no_surat_jalan (For cases where surat_jalan_id might not match)
echo "2. LINKING BY NO_SURAT_JALAN (for remaining unlinked):\n";

$prospekStillWithoutTandaTerima = Prospek::whereNull('tanda_terima_id')
    ->whereNotNull('no_surat_jalan')
    ->where('no_surat_jalan', '!=', '')
    ->get();

echo "Found " . $prospekStillWithoutTandaTerima->count() . " prospek records still without tanda_terima_id but with no_surat_jalan\n\n";

$linkedByNoCount = 0;

foreach ($prospekStillWithoutTandaTerima as $prospek) {
    // Find TandaTerima with matching no_surat_jalan
    $tandaTerima = TandaTerima::where('no_surat_jalan', $prospek->no_surat_jalan)->first();
    
    if ($tandaTerima) {
        echo "Linking Prospek ID {$prospek->id} (no_surat_jalan: {$prospek->no_surat_jalan}) to TandaTerima ID {$tandaTerima->id}";
        if ($tandaTerima->term) {
            echo " (term: {$tandaTerima->term})";
        }
        echo "\n";
        
        // Update prospek with tanda_terima_id
        try {
            $prospek->tanda_terima_id = $tandaTerima->id;
            $prospek->save();
            $linkedByNoCount++;
        } catch (Exception $e) {
            $errors[] = "Failed to link Prospek ID {$prospek->id}: " . $e->getMessage();
            echo "  ERROR: " . $e->getMessage() . "\n";
        }
    }
}

echo "\nMethod 2 Results: Successfully linked {$linkedByNoCount} prospek records by no_surat_jalan\n\n";

// Summary
echo "=== SUMMARY ===\n";
echo "Total linked by surat_jalan_id: {$linkedCount}\n";
echo "Total linked by no_surat_jalan: {$linkedByNoCount}\n";
echo "Total linked: " . ($linkedCount + $linkedByNoCount) . "\n";

if (count($errors) > 0) {
    echo "\nErrors encountered:\n";
    foreach ($errors as $error) {
        echo "- {$error}\n";
    }
}

// Verification: Show prospek with term data now available
echo "\n=== VERIFICATION ===\n";
echo "Prospek records that now have access to term data:\n";

$prospekWithTerm = Prospek::with('tandaTerima')
    ->whereHas('tandaTerima', function($query) {
        $query->whereNotNull('term');
    })
    ->get();

foreach ($prospekWithTerm as $prospek) {
    echo "Prospek ID: {$prospek->id}, TandaTerima ID: {$prospek->tanda_terima_id}, Term: {$prospek->tandaTerima->term}\n";
}

echo "\n=== AUTO-LINKING COMPLETE ===\n";