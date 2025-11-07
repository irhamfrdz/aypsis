<?php

require_once 'vendor/autoload.php';

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING PRANOTA SURAT JALAN PIVOT TABLE ===\n\n";

try {
    // Check if pivot table exists
    $pivotTableExists = \Illuminate\Support\Facades\Schema::hasTable('pranota_surat_jalan_items');
    echo "pranota_surat_jalan_items table exists: " . ($pivotTableExists ? 'YES' : 'NO') . "\n";
    
    if ($pivotTableExists) {
        // Check table structure
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('pranota_surat_jalan_items');
        echo "Table columns: " . implode(', ', $columns) . "\n";
        
        // Check row count
        $rowCount = \Illuminate\Support\Facades\DB::table('pranota_surat_jalan_items')->count();
        echo "Total rows: $rowCount\n";
        
        if ($rowCount > 0) {
            // Show sample data
            $sample = \Illuminate\Support\Facades\DB::table('pranota_surat_jalan_items')
                ->limit(5)
                ->get();
            echo "\nSample data:\n";
            foreach ($sample as $row) {
                echo "ID: {$row->id}, Pranota: {$row->pranota_surat_jalan_id}, SuratJalan: {$row->surat_jalan_id}\n";
            }
        }
    }
    
    // Check current relationships in use
    echo "\n=== CHECKING CURRENT RELATIONSHIPS ===\n";
    
    // Check surat jalan with pranota_surat_jalan_id
    $suratJalanWithPranota = \Illuminate\Support\Facades\DB::table('surat_jalans')
        ->whereNotNull('pranota_surat_jalan_id')
        ->count();
    echo "Surat jalan with pranota_surat_jalan_id: $suratJalanWithPranota\n";
    
    // Check pranota with surat_jalan_id
    $pranotaWithSuratJalan = \Illuminate\Support\Facades\DB::table('pranota_surat_jalans')
        ->whereNotNull('surat_jalan_id')
        ->count();
    echo "Pranota with surat_jalan_id: $pranotaWithSuratJalan\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";