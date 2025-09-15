<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== TESTING GATE FUNCTIONALITY ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

// Test Gate::allows for dashboard
$dashboardAllowed = Gate::allows('dashboard', $user);
echo "Gate::allows('dashboard'): " . ($dashboardAllowed ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test Gate::allows for master-user
$masterUserAllowed = Gate::allows('master-user', $user);
echo "Gate::allows('master-user'): " . ($masterUserAllowed ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test user hasPermissionTo method
$hasDashboardPerm = $user->hasPermissionTo('dashboard');
echo "User hasPermissionTo('dashboard'): " . ($hasDashboardPerm ? '✅ YES' : '❌ NO') . "\n";

$hasMasterUserPerm = $user->hasPermissionTo('master-user');
echo "User hasPermissionTo('master-user'): " . ($hasMasterUserPerm ? '✅ YES' : '❌ NO') . "\n";

// Show user's permissions
echo "\n=== USER'S CURRENT PERMISSIONS ===\n";
$userPermissions = $user->permissions;
if ($userPermissions->count() > 0) {
    foreach ($userPermissions as $perm) {
        echo "- {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "❌ User has no permissions assigned\n";
}

// Test @can directive simulation
echo "\n=== BLADE @can DIRECTIVE SIMULATION ===\n";
echo "@can('dashboard'): " . ($user->can('dashboard') ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo "@can('master-user'): " . ($user->can('master-user') ? '✅ ALLOWED' : '❌ DENIED') . "\n";
