<?php

require_once 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFYING PRANOTA SURAT JALAN RESTORATION ===\n\n";

try {
    // Check if pivot table exists
    $pivotTableExists = \Illuminate\Support\Facades\Schema::hasTable('pranota_surat_jalan_items');
    echo "pranota_surat_jalan_items table exists: " . ($pivotTableExists ? 'YES' : 'NO') . "\n";
    
    if ($pivotTableExists) {
        $rowCount = \Illuminate\Support\Facades\DB::table('pranota_surat_jalan_items')->count();
        echo "Pivot table rows: $rowCount\n";
    }
    
    // Check if surat_jalan_id field exists in pranota_surat_jalans
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('pranota_surat_jalans');
    $hasSuratJalanId = in_array('surat_jalan_id', $columns);
    echo "pranota_surat_jalans has surat_jalan_id field: " . ($hasSuratJalanId ? 'YES' : 'NO') . "\n";
    
    // Test relationship
    $pranota = \App\Models\PranotaSuratJalan::first();
    if ($pranota) {
        echo "First pranota: {$pranota->nomor_pranota}\n";
        
        // Test relationship method
        if (method_exists($pranota, 'suratJalans')) {
            echo "suratJalans() relationship method: EXISTS\n";
            $suratJalansCount = $pranota->suratJalans()->count();
            echo "Surat jalans count for first pranota: $suratJalansCount\n";
        }
    }
    
    // Check migration status
    echo "\n=== MIGRATION STATUS ===\n";
    $migrations = \Illuminate\Support\Facades\DB::table('migrations')
        ->where('migration', 'like', '%pranota%surat%jalan%')
        ->orderBy('batch', 'desc')
        ->get();
        
    foreach ($migrations as $migration) {
        echo "Migration: {$migration->migration} (Batch: {$migration->batch})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICATION COMPLETED ===\n";