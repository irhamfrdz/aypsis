<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "Adding Surat Jalan permissions to admin user...\n\n";

try {
    // Define permissions for Surat Jalan
    $permissions = [
        [
            'name' => 'surat-jalan-view',
            'description' => 'View surat jalan'
        ],
        [
            'name' => 'surat-jalan-create',
            'description' => 'Create surat jalan'
        ],
        [
            'name' => 'surat-jalan-update',
            'description' => 'Update surat jalan'
        ],
        [
            'name' => 'surat-jalan-delete',
            'description' => 'Delete surat jalan'
        ]
    ];

    echo "Step 1: Creating permissions if they don't exist...\n";
    
    foreach ($permissions as $permissionData) {
        $permission = Permission::firstOrCreate(
            ['name' => $permissionData['name']],
            ['description' => $permissionData['description']]
        );
        
        echo "- Permission '{$permission->name}' " . ($permission->wasRecentlyCreated ? "created" : "already exists") . "\n";
    }

    echo "\nStep 2: Finding admin user...\n";
    
    // Find admin user (assuming username 'admin' or you can change this)
    $adminUser = User::where('username', 'admin')->first();
    
    if (!$adminUser) {
        // Try to find by email if username doesn't exist
        $adminUser = User::where('email', 'admin@admin.com')->first();
    }
    
    if (!$adminUser) {
        // Try to find the first user with admin role or first user
        $adminUser = User::where('role', 'admin')->first();
    }
    
    if (!$adminUser) {
        echo "Error: Could not find admin user. Please check the user credentials.\n";
        echo "Available users:\n";
        $users = User::select('id', 'username', 'email', 'role')->get();
        foreach ($users as $user) {
            echo "- ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role}\n";
        }
        exit(1);
    }

    echo "Found admin user: {$adminUser->username} (ID: {$adminUser->id})\n";

    echo "\nStep 3: Assigning permissions to admin user...\n";
    
    foreach ($permissions as $permissionData) {
        $permission = Permission::where('name', $permissionData['name'])->first();
        
        // Check if user already has this permission
        if ($adminUser->hasPermissionTo($permission->name)) {
            echo "- Permission '{$permission->name}' already assigned to admin\n";
        } else {
            // Attach permission to user using the pivot table
            $adminUser->permissions()->attach($permission->id);
            echo "- Permission '{$permission->name}' assigned to admin\n";
        }
    }

    echo "\nStep 4: Verifying permissions...\n";
    
    // Refresh the user's permissions
    $adminUser->load('permissions');
    
    foreach ($permissions as $permissionData) {
        $hasPermission = $adminUser->hasPermissionTo($permissionData['name']);
        echo "- {$permissionData['name']}: " . ($hasPermission ? "✓ GRANTED" : "✗ NOT FOUND") . "\n";
    }

    echo "\n✅ Surat Jalan permissions successfully added to admin user!\n";
    echo "\nAdmin user can now access:\n";
    echo "- View Surat Jalan (surat-jalan-view)\n";
    echo "- Create Surat Jalan (surat-jalan-create)\n";
    echo "- Update Surat Jalan (surat-jalan-update)\n";
    echo "- Delete Surat Jalan (surat-jalan-delete)\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}