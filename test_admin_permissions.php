<?php

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

try {
    // Get admin user
    $admin = User::where('username', 'admin')->with('permissions')->first();
    
    if (!$admin) {
        echo "Admin user not found!\n";
        exit(1);
    }
    
    echo "Admin user found: ID {$admin->id}, Username: {$admin->username}\n";
    echo "Total permissions: " . $admin->permissions->count() . "\n\n";
    
    // Test specific permissions
    $testPermissions = [
        'prospek-kapal-view',
        'prospek-kapal-create', 
        'prospek-kapal-update',
        'prospek-kapal-delete'
    ];
    
    echo "Testing permissions:\n";
    foreach ($testPermissions as $permission) {
        $canResult = $admin->can($permission);
        $hasPermResult = $admin->hasPermissionTo($permission);
        echo "- {$permission}: can() = " . ($canResult ? 'TRUE' : 'FALSE') . ", hasPermissionTo() = " . ($hasPermResult ? 'TRUE' : 'FALSE') . "\n";
    }
    
    echo "\nAll prospek-kapal permissions for admin:\n";
    $prospekPermissions = $admin->permissions->filter(function($perm) {
        return strpos($perm->name, 'prospek-kapal') !== false;
    });
    
    foreach ($prospekPermissions as $perm) {
        echo "- {$perm->name}: {$perm->description}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}