<?php

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "=== CHECKING IF APPROVAL-SURAT-JALAN PERMISSIONS EXIST ===\n\n";
    
    // Check if these permissions exist
    $expectedPermissions = [
        'approval-surat-jalan-view',
        'approval-surat-jalan-approve',
        'approval-surat-jalan-reject',
        'approval-surat-jalan-print',
        'approval-surat-jalan-export'
    ];
    
    foreach ($expectedPermissions as $permName) {
        $exists = App\Models\Permission::where('name', $permName)->exists();
        echo "$permName: " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";
    }
    
    echo "\n=== ALTERNATIVE: CHECKING surat-jalan-approval PATTERN ===\n\n";
    
    $alternativePermissions = [
        'surat-jalan-approval-view',
        'surat-jalan-approval-approve',
        'surat-jalan-approval-reject',
        'surat-jalan-approval-print',
        'surat-jalan-approval-export'
    ];
    
    foreach ($alternativePermissions as $permName) {
        $exists = App\Models\Permission::where('name', $permName)->exists();
        echo "$permName: " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}