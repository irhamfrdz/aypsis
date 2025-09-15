<?php

// Check user permissions in custom permission system
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Checking User test4 Permissions in Custom System\n";
echo "==================================================\n\n";

// Get user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "👤 User test4 found (ID: {$user->id})\n\n";

// Check user_permissions table
echo "📋 Checking user_permissions table:\n";
$userPermissions = DB::table('user_permissions')
    ->where('user_id', $user->id)
    ->get();

if ($userPermissions->count() > 0) {
    echo "  Found " . $userPermissions->count() . " permission records:\n";
    foreach ($userPermissions as $perm) {
        echo "    - Permission ID: {$perm->permission_id}\n";
    }
} else {
    echo "  ❌ No permissions found in user_permissions table\n";
}

// Check permissions table for pranota-related permissions
echo "\n🔍 Checking permissions table for pranota permissions:\n";
$pranotaPerms = DB::table('permissions')
    ->where('name', 'like', '%pranota%')
    ->get();

if ($pranotaPerms->count() > 0) {
    echo "  Found " . $pranotaPerms->count() . " pranota-related permissions:\n";
    foreach ($pranotaPerms as $perm) {
        echo "    - {$perm->name} (ID: {$perm->id})\n";

        // Check if user has this permission
        $hasPermission = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $perm->id)
            ->exists();

        echo "      User has this permission: " . ($hasPermission ? '✅ YES' : '❌ NO') . "\n";
    }
} else {
    echo "  ❌ No pranota permissions found in permissions table\n";
}

// Check for specific pranota-supir permissions
$specificPerms = ['pranota-supir', 'pranota-supir.view', 'pranota-supir.create'];
echo "\n🔍 Checking for specific pranota-supir permissions:\n";
foreach ($specificPerms as $permName) {
    $perm = DB::table('permissions')->where('name', $permName)->first();
    if ($perm) {
        echo "  ✅ {$permName} exists (ID: {$perm->id})\n";

        // Check if user has this permission
        $hasPermission = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $perm->id)
            ->exists();

        echo "    User test4 has permission: " . ($hasPermission ? '✅ YES' : '❌ NO') . "\n";
    } else {
        echo "  ❌ {$permName} does NOT exist\n";
    }
}

// Check role_user table for user roles
echo "\n👥 Checking user roles:\n";
$userRoles = DB::table('role_user')
    ->join('roles', 'role_user.role_id', '=', 'roles.id')
    ->where('role_user.user_id', $user->id)
    ->select('roles.name', 'roles.id')
    ->get();

if ($userRoles->count() > 0) {
    echo "  User test4 has " . $userRoles->count() . " roles:\n";
    foreach ($userRoles as $role) {
        echo "    - {$role->name} (ID: {$role->id})\n";
    }
} else {
    echo "  ❌ User test4 has no roles assigned\n";
}

// Check permission_role table for role permissions
echo "\n🔗 Checking role permissions:\n";
if ($userRoles->count() > 0) {
    foreach ($userRoles as $role) {
        $rolePerms = DB::table('permission_role')
            ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
            ->where('permission_role.role_id', $role->id)
            ->select('permissions.name')
            ->get();

        echo "  Role '{$role->name}' has " . $rolePerms->count() . " permissions:\n";
        foreach ($rolePerms as $perm) {
            echo "    - {$perm->name}\n";
        }
    }
}

echo "\n🔧 ANALYSIS:\n";
$hasPranotaPerm = false;
foreach ($specificPerms as $permName) {
    $perm = DB::table('permissions')->where('name', $permName)->first();
    if ($perm) {
        $hasPermission = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $perm->id)
            ->exists();
        if ($hasPermission) {
            $hasPranotaPerm = true;
            break;
        }
    }
}

if ($hasPranotaPerm) {
    echo "✅ User test4 has pranota-supir permission in database\n";
    echo "💡 The issue may be in the sidebar logic or User model methods\n";
} else {
    echo "❌ User test4 does NOT have pranota-supir permission in database\n";
    echo "💡 SOLUTION: Grant the permission to user test4\n";
}

echo "\n🔧 RECOMMENDATIONS:\n";
echo "1. If permission exists but not working: Check User model hasPermissionTo() method\n";
echo "2. If permission missing: Add permission to user_permissions table\n";
echo "3. Clear application cache: php artisan cache:clear\n";

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
