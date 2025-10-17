<?php
/**
 * Manual Permission Assignment Script for Server
 * Use this if automatic scripts fail
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Manual Permission Assignment - Vendor Kontainer Sewa\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Check if user table exists
try {
    $userCount = DB::table('users')->count();
    echo "✅ Users table found: {$userCount} users\n";
} catch (Exception $e) {
    echo "❌ Cannot access users table: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if permissions table exists
try {
    $permCount = DB::table('permissions')->count();
    echo "✅ Permissions table found: {$permCount} permissions\n";
} catch (Exception $e) {
    echo "❌ Cannot access permissions table: " . $e->getMessage() . "\n";
    exit(1);
}

// List available users
echo "\n📋 Available Users:\n";
$users = DB::table('users')->select('id', 'username', 'email')->get();
foreach ($users as $user) {
    echo "   {$user->id}. {$user->username} ({$user->email})\n";
}

// Prompt for user ID
echo "\n🎯 Enter User ID to assign permissions to (or press Enter for admin): ";
$handle = fopen("php://stdin", "r");
$input = trim(fgets($handle));
fclose($handle);

// Find target user
$targetUserId = null;
if (empty($input)) {
    // Try to find admin
    $admin = DB::table('users')->where('username', 'admin')->first();
    if ($admin) {
        $targetUserId = $admin->id;
        echo "✅ Found admin user: {$admin->username} (ID: {$admin->id})\n";
    } else {
        // Get first user
        $firstUser = DB::table('users')->first();
        if ($firstUser) {
            $targetUserId = $firstUser->id;
            echo "⚠ Admin not found, using first user: {$firstUser->username} (ID: {$firstUser->id})\n";
        }
    }
} else {
    $user = DB::table('users')->find($input);
    if ($user) {
        $targetUserId = $user->id;
        echo "✅ Selected user: {$user->username} (ID: {$user->id})\n";
    } else {
        echo "❌ User ID {$input} not found!\n";
        exit(1);
    }
}

if (!$targetUserId) {
    echo "❌ No user found to assign permissions to!\n";
    exit(1);
}

// Define permissions
$permissions = [
    'vendor-kontainer-sewa-view',
    'vendor-kontainer-sewa-create',
    'vendor-kontainer-sewa-edit',
    'vendor-kontainer-sewa-delete'
];

echo "\n🔧 Creating/Finding Permissions:\n";

// Create or find permissions
$permissionIds = [];
foreach ($permissions as $permName) {
    $perm = DB::table('permissions')->where('name', $permName)->first();
    
    if (!$perm) {
        // Create permission
        $permId = DB::table('permissions')->insertGetId([
            'name' => $permName,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ➕ Created: {$permName} (ID: {$permId})\n";
        $permissionIds[$permName] = $permId;
    } else {
        echo "   ✅ Found: {$permName} (ID: {$perm->id})\n";
        $permissionIds[$permName] = $perm->id;
    }
}

echo "\n🎯 Assigning Permissions:\n";

// Assign permissions
foreach ($permissionIds as $permName => $permId) {
    // Check if already assigned
    $exists = DB::table('model_has_permissions')
        ->where('permission_id', $permId)
        ->where('model_type', 'App\\Models\\User')
        ->where('model_id', $targetUserId)
        ->exists();
    
    if (!$exists) {
        DB::table('model_has_permissions')->insert([
            'permission_id' => $permId,
            'model_type' => 'App\\Models\\User',
            'model_id' => $targetUserId
        ]);
        echo "   ➕ Assigned: {$permName}\n";
    } else {
        echo "   ✅ Already has: {$permName}\n";
    }
}

echo "\n🔍 Verification:\n";

// Verify assignments
$assignedPermissions = DB::table('model_has_permissions')
    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
    ->where('model_type', 'App\\Models\\User')
    ->where('model_id', $targetUserId)
    ->whereIn('permissions.name', $permissions)
    ->pluck('permissions.name')
    ->toArray();

foreach ($permissions as $perm) {
    $has = in_array($perm, $assignedPermissions);
    echo "   " . ($has ? "✅" : "❌") . " {$perm}\n";
}

$successCount = count($assignedPermissions);
echo "\n📊 Summary: {$successCount}/4 permissions assigned\n";

if ($successCount === 4) {
    echo "\n🎉 SUCCESS! All permissions assigned.\n";
    echo "🌐 You can now access: /vendor-kontainer-sewa\n";
} else {
    echo "\n⚠ WARNING: Not all permissions assigned!\n";
    echo "Missing: " . implode(', ', array_diff($permissions, $assignedPermissions)) . "\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "✨ Manual assignment completed!\n";