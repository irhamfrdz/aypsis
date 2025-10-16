<?php

// Simple permission assignment script
require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Permission;

try {
    echo "🔧 Adding permissions for Pranota Surat Jalan...\n\n";
    
    // Get the latest permission IDs
    $permissions = Permission::where('name', 'LIKE', 'pranota-surat-jalan-%')->get();
    
    echo "Found permissions:\n";
    foreach ($permissions as $perm) {
        echo "- ID: {$perm->id}, Name: {$perm->name}\n";
    }
    
    // Get admin user (ID 1)
    $adminUser = User::find(1);
    if (!$adminUser) {
        echo "❌ Admin user (ID: 1) not found!\n";
        exit(1);
    }
    
    echo "\nAssigning permissions to user: {$adminUser->name}\n";
    
    // Assign permissions
    foreach ($permissions as $permission) {
        // Check if already assigned
        $exists = $adminUser->permissions()->where('permission_id', $permission->id)->exists();
        
        if (!$exists) {
            $adminUser->permissions()->attach($permission->id);
            echo "✅ Assigned: {$permission->name}\n";
        } else {
            echo "⚠️  Already assigned: {$permission->name}\n";
        }
    }
    
    echo "\n🎉 Permission assignment completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}