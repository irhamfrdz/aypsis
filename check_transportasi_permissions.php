<?php

// Simple script to check permissions
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Checking Master Transportasi Permissions:\n";
    echo "=========================================\n";
    
    $permissions = DB::table('permissions')
        ->where('name', 'like', 'master-transportasi%')
        ->pluck('name')
        ->toArray();
    
    if (empty($permissions)) {
        echo "❌ No master-transportasi permissions found!\n";
    } else {
        echo "✅ Found " . count($permissions) . " permissions:\n";
        foreach ($permissions as $permission) {
            echo "   - {$permission}\n";
        }
    }
    
    // Also check master-tujuan-kirim permissions for comparison
    echo "\nMaster Tujuan Kirim Permissions (for comparison):\n";
    echo "================================================\n";
    
    $tujuanPermissions = DB::table('permissions')
        ->where('name', 'like', 'master-tujuan-kirim%')
        ->pluck('name')
        ->toArray();
    
    if (empty($tujuanPermissions)) {
        echo "❌ No master-tujuan-kirim permissions found!\n";
    } else {
        echo "✅ Found " . count($tujuanPermissions) . " permissions:\n";
        foreach ($tujuanPermissions as $permission) {
            echo "   - {$permission}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}