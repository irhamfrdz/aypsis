<?php

try {
    // Minimal bootstrap
    require_once 'vendor/autoload.php';
    
    // Create minimal app instance
    $app = require_once 'bootstrap/app.php';
    
    // Get the Illuminate kernel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "Available approval-surat-jalan permissions:\n";
    $permissions = App\Models\Permission::where('name', 'like', 'approval-surat-jalan%')->pluck('name')->toArray();
    
    if (empty($permissions)) {
        echo "No approval-surat-jalan permissions found.\n";
        echo "Checking related patterns...\n";
        
        $related = App\Models\Permission::where('name', 'like', '%approval%surat%jalan%')
                                      ->orWhere('name', 'like', '%surat%jalan%approval%')
                                      ->pluck('name')->toArray();
        
        foreach ($related as $perm) {
            echo "- $perm\n";
        }
    } else {
        foreach ($permissions as $permission) {
            echo "- $permission\n";
        }
    }
    
    echo "\nTotal: " . count($permissions) . " permissions\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}