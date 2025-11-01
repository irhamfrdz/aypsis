<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // List of permissions for pranota uang kenek
    $permissions = [
        'pranota-uang-kenek-view',
        'pranota-uang-kenek-create', 
        'pranota-uang-kenek-edit',
        'pranota-uang-kenek-update',
        'pranota-uang-kenek-delete',
        'pranota-uang-kenek-approve',
        'pranota-uang-kenek-mark-paid'
    ];

    // Get admin user (user with role admin)
    $adminUser = DB::table('users')->where('role', 'admin')->first();
    
    if (!$adminUser) {
        echo "Admin user not found. Looking for user with username 'admin'...\n";
        
        // Try to find admin by username
        $adminUser = DB::table('users')->where('username', 'admin')->first();
        
        if (!$adminUser) {
            echo "No admin user found. Available users:\n";
            $users = DB::table('users')->select('id', 'username', 'role')->get();
            foreach ($users as $user) {
                echo "- ID: {$user->id}, Username: {$user->username}, Role: {$user->role}\n";
            }
            exit;
        }
    }

    echo "Found admin user: {$adminUser->username} (Role: {$adminUser->role})\n";

    // First, create permissions in permissions table if they don't exist
    foreach ($permissions as $permission) {
        $existingPermission = DB::table('permissions')
            ->where('name', $permission)
            ->first();

        if (!$existingPermission) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $permission,
                'description' => ucwords(str_replace('-', ' ', $permission)),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Created permission: {$permission} (ID: {$permissionId})\n";
        } else {
            echo "- Permission already exists: {$permission} (ID: {$existingPermission->id})\n";
        }
    }

    // Now assign permissions to admin user
    foreach ($permissions as $permission) {
        // Get permission ID
        $permissionRecord = DB::table('permissions')
            ->where('name', $permission)
            ->first();

        if (!$permissionRecord) {
            echo "❌ Permission {$permission} not found in permissions table\n";
            continue;
        }

        // Check if user already has this permission
        $existingUserPermission = DB::table('user_permissions')
            ->where('user_id', $adminUser->id)
            ->where('permission_id', $permissionRecord->id)
            ->first();

        if (!$existingUserPermission) {
            DB::table('user_permissions')->insert([
                'user_id' => $adminUser->id,
                'permission_id' => $permissionRecord->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Assigned permission to admin: {$permission}\n";
        } else {
            echo "- Admin already has permission: {$permission}\n";
        }
    }

    echo "\n✅ Successfully processed all pranota uang kenek permissions for admin user!\n";
    echo "\nPermissions processed:\n";
    foreach ($permissions as $permission) {
        echo "  • {$permission}\n";
    }

    // Show admin user's current permissions count
    $totalPermissions = DB::table('user_permissions')
        ->where('user_id', $adminUser->id)
        ->count();
    
    echo "\n📊 Admin user now has {$totalPermissions} total permissions.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}