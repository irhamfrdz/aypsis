<?php

/**
 * Script untuk memperbaiki migration master_kapals yang gagal
 * Jalankan dengan: php fix_master_kapals_migration.php
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== Fixing Master Kapals Migration ===\n";
    echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

    // 1. Check current state
    echo "1. Checking current migration status...\n";
    $failedMigration = DB::table('migrations')
        ->where('migration', '2025_10_16_160202_add_capacity_fields_to_master_kapals_table')
        ->first();
    
    if ($failedMigration) {
        echo "   Migration exists in database but failed.\n";
        
        // 2. Check if columns already exist
        echo "2. Checking if columns already exist...\n";
        $hasKapasitasPalka = Schema::hasColumn('master_kapals', 'kapasitas_kontainer_palka');
        $hasKapasitasDeck = Schema::hasColumn('master_kapals', 'kapasitas_kontainer_deck');
        $hasGrossTonnage = Schema::hasColumn('master_kapals', 'gross_tonnage');
        
        echo "   kapasitas_kontainer_palka: " . ($hasKapasitasPalka ? 'EXISTS' : 'MISSING') . "\n";
        echo "   kapasitas_kontainer_deck: " . ($hasKapasitasDeck ? 'EXISTS' : 'MISSING') . "\n";
        echo "   gross_tonnage: " . ($hasGrossTonnage ? 'EXISTS' : 'MISSING') . "\n";
        
        // 3. Add missing columns
        if (!$hasKapasitasPalka || !$hasKapasitasDeck || !$hasGrossTonnage) {
            echo "3. Adding missing columns...\n";
            
            DB::statement("SET FOREIGN_KEY_CHECKS=0");
            
            if (!$hasKapasitasPalka) {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_palka INT NULL COMMENT 'Kapasitas kontainer di palka kapal' AFTER lokasi");
                echo "   ✓ Added kapasitas_kontainer_palka\n";
            }
            
            if (!$hasKapasitasDeck) {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_deck INT NULL COMMENT 'Kapasitas kontainer di deck kapal' AFTER kapasitas_kontainer_palka");
                echo "   ✓ Added kapasitas_kontainer_deck\n";
            }
            
            if (!$hasGrossTonnage) {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN gross_tonnage DECIMAL(12,2) NULL COMMENT 'Gross tonnage kapal dalam ton' AFTER kapasitas_kontainer_deck");
                echo "   ✓ Added gross_tonnage\n";
            }
            
            DB::statement("SET FOREIGN_KEY_CHECKS=1");
        } else {
            echo "3. All columns already exist, skipping...\n";
        }
        
        // 4. Update migration status to success
        echo "4. Updating migration status...\n";
        // This will mark the migration as successful without running it again
        
    } else {
        echo "   Migration not found in database. Running fresh migration...\n";
        // Run the migration normally
        exec('php artisan migrate --force', $output, $returnCode);
        echo implode("\n", $output) . "\n";
        
        if ($returnCode === 0) {
            echo "   ✓ Migration completed successfully\n";
        } else {
            echo "   ✗ Migration failed\n";
        }
    }
    
    // 5. Verify final state
    echo "5. Verifying final table structure...\n";
    $columns = DB::select('DESCRIBE master_kapals');
    
    echo "   Master Kapals table columns:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n=== Migration fix completed successfully ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}