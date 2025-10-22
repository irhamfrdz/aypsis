<?php

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;

try {
    echo "Testing NIK auto-generation system...\n\n";
    
    // Get current highest NIK
    $currentHighest = Karyawan::whereRaw('nik REGEXP \'^[0-9]+$\'')
                              ->orderByRaw('CAST(nik AS UNSIGNED) DESC')
                              ->value('nik');
    
    echo "Current highest numeric NIK in database: " . ($currentHighest ?: 'None found') . "\n";
    
    // Get current highest NIK in 1502+ range
    $currentHighestInRange = Karyawan::whereRaw('nik REGEXP \'^[0-9]+$\'')
                                     ->whereRaw('CAST(nik AS UNSIGNED) >= 1502')
                                     ->orderByRaw('CAST(nik AS UNSIGNED) DESC')
                                     ->value('nik');
    
    echo "Current highest NIK in 1502+ range: " . ($currentHighestInRange ?: 'None found') . "\n";
    
    // Test generateNextNik method
    $nextNik = Karyawan::generateNextNik();
    echo "Next NIK that will be generated: " . $nextNik . "\n\n";
    
    // Show existing NIKs in 1502+ range
    $existingInRange = Karyawan::whereRaw('nik REGEXP \'^[0-9]+$\'')
                               ->whereRaw('CAST(nik AS UNSIGNED) >= 1502')
                               ->whereRaw('CAST(nik AS UNSIGNED) <= 1510')
                               ->orderByRaw('CAST(nik AS UNSIGNED) ASC')
                               ->pluck('nik');
    
    echo "Existing NIKs in range 1502-1510: " . $existingInRange->implode(', ') . "\n\n";
    
    echo "Note: The system will use the new 1502+ sequence for new employees.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}