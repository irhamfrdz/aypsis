<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== TESTING USER ADMIN PERMISSIONS ===\n";

$userAdmin = User::where('username', 'user_admin')->first();

if ($userAdmin) {
    echo "User admin found: " . $userAdmin->username . "\n";
    
    // Test specific permission
    $hasPermission = $userAdmin->can('master-tujuan-kirim-view');
    echo "Can access master-tujuan-kirim-view: " . ($hasPermission ? 'YES' : 'NO') . "\n";
    
    // Check if permission exists in database
    $permission = \App\Models\Permission::where('name', 'master-tujuan-kirim-view')->first();
    if ($permission) {
        echo "Permission exists in database: YES\n";
        echo "Permission ID: " . $permission->id . "\n";
        
        // Check if user has this permission in pivot table
        $userHasPermission = $userAdmin->permissions()->where('permission_id', $permission->id)->exists();
        echo "User has permission in pivot table: " . ($userHasPermission ? 'YES' : 'NO') . "\n";
        
        // Show all user permissions count
        echo "Total user permissions: " . $userAdmin->permissions()->count() . "\n";
    } else {
        echo "Permission NOT found in database\n";
    }
} else {
    echo "User admin not found\n";
}