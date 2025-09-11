<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Gate;

echo "Testing user without permissions...\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit(1);
}

echo "Found user: {$user->name} (ID: {$user->id})\n";

// Check current permissions
$permissions = $user->permissions->pluck('name')->toArray();
echo "Current permissions: " . implode(', ', $permissions) . "\n";
echo "Permission count: " . count($permissions) . "\n";

// Test dashboard permission
$hasDashboardPerm = $user->hasPermissionTo('dashboard');
echo "hasPermissionTo('dashboard'): " . ($hasDashboardPerm ? '✅ YES' : '❌ NO') . "\n";

$userCanDashboard = $user->can('dashboard');
echo "user->can('dashboard'): " . ($userCanDashboard ? '✅ YES' : '❌ NO') . "\n";

$gateAllows = Gate::allows('dashboard', $user);
echo "Gate::allows('dashboard', \$user): " . ($gateAllows ? '✅ YES' : '❌ NO') . "\n";

// Check if user has any permissions at all
$hasAnyPermissions = count($permissions) > 0;
echo "Has any permissions: " . ($hasAnyPermissions ? '✅ YES' : '❌ NO') . "\n";

// Test what happens if we remove all permissions temporarily
if ($hasAnyPermissions) {
    echo "\n=== TESTING USER WITHOUT ANY PERMISSIONS ===\n";

    // Simulate user without permissions
    $userWithoutPerms = clone $user;
    $userWithoutPerms->permissions = collect([]);

    $noPermHasDashboard = $userWithoutPerms->hasPermissionTo('dashboard');
    echo "User without permissions - hasPermissionTo('dashboard'): " . ($noPermHasDashboard ? '✅ YES' : '❌ NO') . "\n";

    $noPermCanDashboard = $userWithoutPerms->can('dashboard');
    echo "User without permissions - can('dashboard'): " . ($noPermCanDashboard ? '✅ YES' : '❌ NO') . "\n";

    $noPermGateAllows = Gate::allows('dashboard', $userWithoutPerms);
    echo "User without permissions - Gate::allows('dashboard'): " . ($noPermGateAllows ? '✅ YES' : '❌ NO') . "\n";
}
