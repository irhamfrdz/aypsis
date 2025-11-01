<?php

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "=== SEARCHING ALL SURAT JALAN RELATED PERMISSIONS ===\n\n";
    
    // Search for all permissions containing 'surat' and 'jalan'
    $suratJalanPerms = App\Models\Permission::where('name', 'like', '%surat%jalan%')
                                           ->orWhere('name', 'like', '%surat-jalan%')
                                           ->pluck('name')->toArray();
    
    echo "All surat-jalan related permissions:\n";
    foreach ($suratJalanPerms as $perm) {
        echo "- $perm\n";
    }
    
    echo "\n=== SEARCHING APPROVAL PATTERNS ===\n\n";
    
    // Search for approval patterns
    $approvalPerms = App\Models\Permission::where('name', 'like', '%approval%')
                                         ->pluck('name')->toArray();
    
    echo "Approval permissions (showing first 20):\n";
    $count = 0;
    foreach ($approvalPerms as $perm) {
        if ($count++ < 20) {
            echo "- $perm\n";
        }
    }
    
    if (count($approvalPerms) > 20) {
        echo "... and " . (count($approvalPerms) - 20) . " more\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}