<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\MasterPricelistOb;

echo "=== Debug Master Pricelist OB ===\n";

// Check data with raw query
$rawData = DB::table('master_pricelist_ob')->get();
echo "Total data dengan raw query: " . $rawData->count() . "\n";

if ($rawData->count() > 0) {
    echo "\nData mentah dari database:\n";
    foreach ($rawData as $item) {
        echo "ID: {$item->id}, Size: {$item->size_kontainer}, Status: {$item->status_kontainer}, Biaya: {$item->biaya}, Keterangan: " . ($item->keterangan ?: 'null') . "\n";
    }
}

// Check dengan Eloquent model
echo "\n=== Testing dengan Eloquent Model ===\n";
try {
    $modelData = MasterPricelistOb::all();
    echo "Total data dengan Eloquent: " . $modelData->count() . "\n";
    
    if ($modelData->count() > 0) {
        $first = $modelData->first();
        echo "\nTesting accessor pada record pertama:\n";
        echo "Size: " . $first->size_kontainer . "\n";
        echo "Status: " . $first->status_kontainer . "\n";
        echo "Biaya: " . $first->biaya . "\n";
        echo "Size Label: " . $first->size_kontainer_label . "\n";
        echo "Status Label: " . $first->status_kontainer_label . "\n";
        echo "Formatted Biaya: " . $first->formatted_biaya . "\n";
        
        echo "\nTesting raw attribute access:\n";
        echo "getAttribute('size_kontainer_label'): " . $first->getAttribute('size_kontainer_label') . "\n";
        echo "getAttribute('status_kontainer_label'): " . $first->getAttribute('status_kontainer_label') . "\n";
        echo "getAttribute('formatted_biaya'): " . $first->getAttribute('formatted_biaya') . "\n";
    }
} catch (Exception $e) {
    echo "Error dengan Eloquent: " . $e->getMessage() . "\n";
}