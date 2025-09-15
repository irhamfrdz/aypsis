<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== TESTING hasPermissionTo METHOD ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

// Test hasPermissionTo method directly
$hasDashboard = $user->hasPermissionTo('dashboard');
echo "hasPermissionTo('dashboard'): " . ($hasDashboard ? '✅ YES' : '❌ NO') . "\n";

$hasMasterUser = $user->hasPermissionTo('master-user');
echo "hasPermissionTo('master-user'): " . ($hasMasterUser ? '✅ YES' : '❌ NO') . "\n";

// Check if permissions are loaded
echo "\n=== CHECKING PERMISSIONS RELATIONSHIP ===\n";
$userWithPermissions = User::with('permissions')->find($user->id);
$permissionsLoaded = $userWithPermissions->permissions;
echo "Permissions loaded: " . ($permissionsLoaded ? '✅ YES' : '❌ NO') . "\n";
echo "Number of permissions: " . $permissionsLoaded->count() . "\n";

// Check specific permissions
$dashboardPerm = $permissionsLoaded->where('name', 'dashboard')->first();
echo "Dashboard permission in collection: " . ($dashboardPerm ? '✅ YES (ID: ' . $dashboardPerm->id . ')' : '❌ NO') . "\n";

$masterUserPerm = $permissionsLoaded->where('name', 'master-user')->first();
echo "Master-user permission in collection: " . ($masterUserPerm ? '✅ YES (ID: ' . $masterUserPerm->id . ')' : '❌ NO') . "\n";

// Test contains method
$containsDashboard = $permissionsLoaded->contains('name', 'dashboard');
echo "Permissions collection contains 'dashboard': " . ($containsDashboard ? '✅ YES' : '❌ NO') . "\n";

$containsMasterUser = $permissionsLoaded->contains('name', 'master-user');
echo "Permissions collection contains 'master-user': " . ($containsMasterUser ? '✅ YES' : '❌ NO') . "\n";

// Test the exact logic from hasPermissionTo
echo "\n=== TESTING EXACT LOGIC FROM hasPermissionTo ===\n";
$resultDashboard = $user->permissions->contains('name', 'dashboard');
echo "\$user->permissions->contains('name', 'dashboard'): " . ($resultDashboard ? '✅ YES' : '❌ NO') . "\n";

$resultMasterUser = $user->permissions->contains('name', 'master-user');
echo "\$user->permissions->contains('name', 'master-user'): " . ($resultMasterUser ? '✅ YES' : '❌ NO') . "\n";
