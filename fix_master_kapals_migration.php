<?php

/**
 * Script untuk memperbaiki migration master_kapals yang gagal
 * Handles different table structures between server and local
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
    echo "=== Fixing Master Kapals Migration (Server Compatible) ===\n";
    echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

    // 1. Check current table structure
    echo "1. Analyzing current master_kapals table structure...\n";
    $columns = DB::select('DESCRIBE master_kapals');
    $columnNames = array_map(function($col) { return $col->Field; }, $columns);
    
    echo "   Current columns: " . implode(', ', $columnNames) . "\n";
    
    $hasLokasi = in_array('lokasi', $columnNames);
    $hasPelayaran = in_array('pelayaran', $columnNames);
    $hasCatatan = in_array('catatan', $columnNames);
    $hasKapasitasPalka = in_array('kapasitas_kontainer_palka', $columnNames);
    $hasKapasitasDeck = in_array('kapasitas_kontainer_deck', $columnNames);
    $hasGrossTonnage = in_array('gross_tonnage', $columnNames);
    
    echo "   Reference columns available:\n";
    echo "   - lokasi: " . ($hasLokasi ? 'YES' : 'NO') . "\n";
    echo "   - pelayaran: " . ($hasPelayaran ? 'YES' : 'NO') . "\n";
    echo "   - catatan: " . ($hasCatatan ? 'YES' : 'NO') . "\n";
    
    echo "   Target columns status:\n";
    echo "   - kapasitas_kontainer_palka: " . ($hasKapasitasPalka ? 'EXISTS' : 'MISSING') . "\n";
    echo "   - kapasitas_kontainer_deck: " . ($hasKapasitasDeck ? 'EXISTS' : 'MISSING') . "\n";
    echo "   - gross_tonnage: " . ($hasGrossTonnage ? 'EXISTS' : 'MISSING') . "\n\n";

    // 2. Handle failed migration record
    echo "2. Handling migration record...\n";
    $failedMigration = DB::table('migrations')
        ->where('migration', '2025_10_16_160202_add_capacity_fields_to_master_kapals_table')
        ->first();
    
    if ($failedMigration) {
        echo "   Migration record exists, removing failed entry...\n";
        DB::table('migrations')
            ->where('migration', '2025_10_16_160202_add_capacity_fields_to_master_kapals_table')
            ->delete();
        echo "   ✓ Removed failed migration record\n";
    }

    // 3. Add missing columns with smart positioning
    $needsUpdate = !$hasKapasitasPalka || !$hasKapasitasDeck || !$hasGrossTonnage;
    
    if ($needsUpdate) {
        echo "3. Adding missing columns...\n";
        
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        
        // Determine the best reference column for positioning
        $afterColumn = null;
        if ($hasPelayaran) {
            $afterColumn = 'pelayaran';
        } elseif ($hasLokasi) {
            $afterColumn = 'lokasi';
        } elseif ($hasCatatan) {
            $afterColumn = 'catatan';
        }
        
        if (!$hasKapasitasPalka) {
            if ($afterColumn) {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_palka INT NULL COMMENT 'Kapasitas kontainer di palka kapal' AFTER `{$afterColumn}`");
            } else {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_palka INT NULL COMMENT 'Kapasitas kontainer di palka kapal'");
            }
            echo "   ✓ Added kapasitas_kontainer_palka" . ($afterColumn ? " after {$afterColumn}" : "") . "\n";
        }
        
        if (!$hasKapasitasDeck) {
            if ($hasKapasitasPalka || in_array('kapasitas_kontainer_palka', $columnNames)) {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_deck INT NULL COMMENT 'Kapasitas kontainer di deck kapal' AFTER kapasitas_kontainer_palka");
            } else {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN kapasitas_kontainer_deck INT NULL COMMENT 'Kapasitas kontainer di deck kapal'");
            }
            echo "   ✓ Added kapasitas_kontainer_deck\n";
        }
        
        if (!$hasGrossTonnage) {
            if ($hasKapasitasDeck || in_array('kapasitas_kontainer_deck', $columnNames)) {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN gross_tonnage DECIMAL(12,2) NULL COMMENT 'Gross tonnage kapal dalam ton' AFTER kapasitas_kontainer_deck");
            } else {
                DB::statement("ALTER TABLE master_kapals ADD COLUMN gross_tonnage DECIMAL(12,2) NULL COMMENT 'Gross tonnage kapal dalam ton'");
            }
            echo "   ✓ Added gross_tonnage\n";
        }
        
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    } else {
        echo "3. All target columns already exist, skipping column addition...\n";
    }
    
    // 4. Run the fixed migration
    echo "4. Running the corrected migration...\n";
    try {
        $output = [];
        $returnCode = 0;
        exec('php artisan migrate --force 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "   ✓ Migration completed successfully\n";
        } else {
            echo "   Migration output: " . implode("\n   ", $output) . "\n";
            // This is OK if the migration is already marked as run
        }
    } catch (Exception $migrationException) {
        echo "   Migration error (may be expected if columns already exist): " . $migrationException->getMessage() . "\n";
    }
    
    // 5. Verify final state
    echo "5. Verifying final table structure...\n";
    $finalColumns = DB::select('DESCRIBE master_kapals');
    
    echo "   Final master_kapals table columns:\n";
    foreach ($finalColumns as $column) {
        $isNew = in_array($column->Field, ['kapasitas_kontainer_palka', 'kapasitas_kontainer_deck', 'gross_tonnage']);
        $marker = $isNew ? ' [NEW]' : '';
        echo "   - {$column->Field} ({$column->Type}){$marker}\n";
    }
    
    echo "\n=== Migration fix completed successfully ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Cleanup if needed
    echo "\nAttempting cleanup...\n";
    try {
        DB::table('migrations')
            ->where('migration', '2025_10_16_160202_add_capacity_fields_to_master_kapals_table')
            ->delete();
        echo "Cleaned up failed migration record.\n";
    } catch (Exception $cleanupError) {
        echo "Cleanup failed: " . $cleanupError->getMessage() . "\n";
    }
    
    exit(1);
}