<?php

/**
 * Script untuk menambah permission Vendor Kontainer Sewa dan assign ke admin
 * Jalankan dengan: php add_vendor_kontainer_sewa_permissions_to_admin.php
 * 
 * Script ini akan:
 * 1. Membuat permissions untuk vendor kontainer sewa
 * 2. Assign permissions ke user admin
 * 3. Verifikasi hasil
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

echo "=== Adding Vendor Kontainer Sewa Permissions ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Define permissions untuk vendor kontainer sewa
    $permissions = [
        'vendor-kontainer-sewa-view' => 'View vendor kontainer sewa',
        'vendor-kontainer-sewa-create' => 'Create vendor kontainer sewa', 
        'vendor-kontainer-sewa-edit' => 'Edit vendor kontainer sewa',
        'vendor-kontainer-sewa-delete' => 'Delete vendor kontainer sewa',
    ];

    echo "1. Creating Vendor Kontainer Sewa permissions...\n";
    
    $createdPermissions = [];
    $existingPermissions = [];
    
    foreach ($permissions as $name => $description) {
        $permission = Permission::firstOrCreate(
            ['name' => $name],
            ['guard_name' => 'web']
        );
        
        if ($permission->wasRecentlyCreated) {
            $createdPermissions[] = $name;
            echo "   ✓ Created permission: {$name}\n";
        } else {
            $existingPermissions[] = $name;
            echo "   → Permission already exists: {$name}\n";
        }
    }
    
    echo "\n";
    echo "   Summary:\n";
    echo "   - Created: " . count($createdPermissions) . " permissions\n";
    echo "   - Existing: " . count($existingPermissions) . " permissions\n";
    echo "   - Total: " . count($permissions) . " permissions\n\n";

    // 2. Find admin users
    echo "2. Finding admin users...\n";
    
    // Try different methods to find admin users
    $adminUsers = collect();
    
    // Method 1: By username 'admin'
    $adminByUsername = User::where('username', 'admin')->first();
    if ($adminByUsername) {
        $adminUsers->push($adminByUsername);
        echo "   ✓ Found admin by username: {$adminByUsername->username} (ID: {$adminByUsername->id})\n";
    }
    
    // Method 2: By email containing 'admin'
    $adminsByEmail = User::where('email', 'like', '%admin%')->get();
    foreach ($adminsByEmail as $admin) {
        if (!$adminUsers->contains('id', $admin->id)) {
            $adminUsers->push($admin);
            echo "   ✓ Found admin by email: {$admin->email} (ID: {$admin->id})\n";
        }
    }
    
    // Method 3: By role 'admin' or 'administrator'
    try {
        $adminRole = Role::whereIn('name', ['admin', 'administrator'])->first();
        if ($adminRole) {
            $adminsByRole = User::role($adminRole->name)->get();
            foreach ($adminsByRole as $admin) {
                if (!$adminUsers->contains('id', $admin->id)) {
                    $adminUsers->push($admin);
                    echo "   ✓ Found admin by role '{$adminRole->name}': {$admin->username} (ID: {$admin->id})\n";
                }
            }
        }
    } catch (Exception $roleException) {
        echo "   → Role-based search skipped (role system may not be fully configured)\n";
    }
    
    // Method 4: By user ID 1 (typically admin)
    $adminById = User::find(1);
    if ($adminById && !$adminUsers->contains('id', $adminById->id)) {
        $adminUsers->push($adminById);
        echo "   ✓ Found admin by ID 1: {$adminById->username} (ID: {$adminById->id})\n";
    }
    
    if ($adminUsers->isEmpty()) {
        echo "   ⚠ No admin users found automatically.\n";
        echo "   Please specify admin user manually:\n";
        
        // Show available users
        $users = User::select('id', 'username', 'email')->limit(10)->get();
        echo "   Available users:\n";
        foreach ($users as $user) {
            echo "   - ID: {$user->id}, Username: {$user->username}, Email: {$user->email}\n";
        }
        echo "\n   You can run this script with specific user ID:\n";
        echo "   php artisan tinker --execute=\"\$user = App\\Models\\User::find(USER_ID); \$user->givePermissionTo(['vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete']);\"\n\n";
    }

    // 3. Assign permissions to admin users
    if ($adminUsers->isNotEmpty()) {
        echo "\n3. Assigning permissions to admin users...\n";
        
        $permissionNames = array_keys($permissions);
        $assignmentResults = [];
        
        foreach ($adminUsers as $admin) {
            try {
                echo "   Processing user: {$admin->username} (ID: {$admin->id})\n";
                
                $newPermissions = [];
                $existingPermissions = [];
                
                foreach ($permissionNames as $permissionName) {
                    if ($admin->hasPermissionTo($permissionName)) {
                        $existingPermissions[] = $permissionName;
                    } else {
                        $newPermissions[] = $permissionName;
                    }
                }
                
                if (!empty($newPermissions)) {
                    $admin->givePermissionTo($newPermissions);
                    echo "     ✓ Assigned " . count($newPermissions) . " new permissions\n";
                    foreach ($newPermissions as $perm) {
                        echo "       - {$perm}\n";
                    }
                }
                
                if (!empty($existingPermissions)) {
                    echo "     → Already has " . count($existingPermissions) . " permissions\n";
                }
                
                $assignmentResults[] = [
                    'user' => $admin->username,
                    'new' => count($newPermissions),
                    'existing' => count($existingPermissions),
                    'total' => count($permissionNames)
                ];
                
            } catch (Exception $assignException) {
                echo "     ✗ Error assigning permissions to {$admin->username}: " . $assignException->getMessage() . "\n";
            }
        }
        
        // 4. Summary
        echo "\n4. Assignment Summary:\n";
        foreach ($assignmentResults as $result) {
            echo "   User: {$result['user']}\n";
            echo "   - New permissions: {$result['new']}\n";
            echo "   - Existing permissions: {$result['existing']}\n";
            echo "   - Total permissions: {$result['total']}\n";
        }
    }

    // 5. Verification
    echo "\n5. Verification...\n";
    
    // Check if permissions exist
    $permissionCount = Permission::whereIn('name', array_keys($permissions))->count();
    echo "   Permissions in database: {$permissionCount}/" . count($permissions) . "\n";
    
    // Check admin users with permissions
    $usersWithPermissions = User::permission(array_keys($permissions))->get();
    echo "   Users with vendor kontainer sewa permissions: {$usersWithPermissions->count()}\n";
    
    foreach ($usersWithPermissions as $user) {
        $userPermissions = $user->getPermissionNames()->filter(function($perm) use ($permissions) {
            return array_key_exists($perm, $permissions);
        });
        echo "   - {$user->username}: " . $userPermissions->count() . "/" . count($permissions) . " permissions\n";
    }

    // 6. Create simple verification script
    echo "\n6. Creating verification script...\n";
    $verificationScript = "<?php
// Quick verification script for vendor kontainer sewa permissions
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\\Contracts\\Console\\Kernel::class);
\$kernel->bootstrap();

use App\\Models\\User;

echo \"=== Vendor Kontainer Sewa Permissions Verification ===\\n\";
\$permissions = ['vendor-kontainer-sewa-view', 'vendor-kontainer-sewa-create', 'vendor-kontainer-sewa-edit', 'vendor-kontainer-sewa-delete'];
\$users = User::permission(\$permissions)->get();

foreach (\$users as \$user) {
    echo \"User: {\$user->username}\\n\";
    foreach (\$permissions as \$perm) {
        \$status = \$user->hasPermissionTo(\$perm) ? '✓' : '✗';
        echo \"  {\$status} {\$perm}\\n\";
    }
    echo \"\\n\";
}
";
    
    file_put_contents('verify_vendor_kontainer_sewa_permissions.php', $verificationScript);
    echo "   ✓ Created verify_vendor_kontainer_sewa_permissions.php\n";
    echo "   Run with: php verify_vendor_kontainer_sewa_permissions.php\n";

    echo "\n=== Vendor Kontainer Sewa Permissions Setup Completed Successfully ===\n";
    echo "You can now access the vendor kontainer sewa module with proper permissions.\n";
    echo "Route: /vendor-kontainer-sewa\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    echo "\nTroubleshooting:\n";
    echo "1. Make sure Spatie Permission package is installed\n";
    echo "2. Check if migrations are up to date: php artisan migrate:status\n";
    echo "3. Verify database connection\n";
    echo "4. Check if vendor kontainer sewa module is properly installed\n";
    
    exit(1);
}

?>