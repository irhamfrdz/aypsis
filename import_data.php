<?php

require_once 'vendor/autoload.php';

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Starting SQL import...\n";
    
    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    echo "Foreign key checks disabled.\n";
    
    // Read SQL file
    $sqlContent = file_get_contents('aypsis3.sql');
    
    if (!$sqlContent) {
        throw new Exception("Could not read SQL file");
    }
    
    echo "SQL file loaded successfully. Size: " . strlen($sqlContent) . " bytes\n";
    
    // Remove MySQL specific comments and commands that might cause issues
    $sqlContent = preg_replace('/\/\*![0-9]{5}.*?\*\/;/', '', $sqlContent);
    $sqlContent = preg_replace('/\/\*.*?\*\//', '', $sqlContent);
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sqlContent)),
        function($statement) {
            return !empty($statement) && 
                   !preg_match('/^--/', $statement) &&
                   !preg_match('/^\/\*/', $statement) &&
                   !preg_match('/^DROP TABLE IF EXISTS/', $statement) &&
                   !preg_match('/^CREATE TABLE/', $statement);
        }
    );
    
    echo "Found " . count($statements) . " SQL statements to execute.\n";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        if (trim($statement) === '') continue;
        
        try {
            // Only execute INSERT and UPDATE statements to preserve our migrated structure
            if (preg_match('/^(INSERT|UPDATE|LOCK TABLES|UNLOCK TABLES|ALTER TABLE.*ENABLE KEYS|ALTER TABLE.*DISABLE KEYS)/i', trim($statement))) {
                DB::statement($statement);
                $successCount++;
                
                if ($successCount % 100 === 0) {
                    echo "Processed $successCount statements...\n";
                }
            }
        } catch (Exception $e) {
            $errorCount++;
            echo "Error in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            
            // Continue with next statement
            continue;
        }
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    echo "Foreign key checks re-enabled.\n";
    
    echo "\nImport completed!\n";
    echo "Successful statements: $successCount\n";
    echo "Errors: $errorCount\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    
    // Try to re-enable foreign key checks even on error
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    } catch (Exception $cleanupError) {
        echo "Could not re-enable foreign key checks: " . $cleanupError->getMessage() . "\n";
    }
}