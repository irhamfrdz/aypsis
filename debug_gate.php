<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

echo "=== DEBUGGING GATE SYSTEM ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

// First, let's test if Gate::before is causing issues
echo "=== TESTING Gate::before ===\n";

// Temporarily remove Gate::before to see if that's the issue
$originalBefore = null;
try {
    // We can't directly access Gate::before, but let's test with a gate that bypasses it
    Gate::define('bypass-test', function (User $testUser) {
        // This should always return true
        return true;
    });

    $bypassResult = Gate::check('bypass-test', $user);
    echo "Bypass test result: " . ($bypassResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

} catch (Exception $e) {
    echo "Exception in bypass test: " . $e->getMessage() . "\n";
}

// Test with a gate that doesn't take parameters
echo "\n=== TESTING PARAMETERLESS GATE ===\n";

Gate::define('no-params', function () {
    return true;
});

$noParamsResult = Gate::check('no-params');
echo "No params gate result: " . ($noParamsResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test with a gate that takes user but ignores it
echo "\n=== TESTING USER IGNORING GATE ===\n";

Gate::define('ignore-user', function (User $ignoredUser) {
    return true;
});

$ignoreUserResult = Gate::check('ignore-user', $user);
echo "Ignore user gate result: " . ($ignoreUserResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Now test the actual dashboard gate
echo "\n=== TESTING DASHBOARD GATE ===\n";

$dashboardResult = Gate::check('dashboard', $user);
echo "Dashboard gate result: " . ($dashboardResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test user->can which works
$userCanResult = $user->can('dashboard');
echo "User can dashboard: " . ($userCanResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Let's check if the issue is with the user object itself
echo "\n=== TESTING USER OBJECT ===\n";
echo "User ID: " . $user->id . "\n";
echo "User name: " . $user->name . "\n";
echo "User permissions count: " . $user->permissions->count() . "\n";
echo "Has dashboard permission: " . ($user->hasPermissionTo('dashboard') ? '✅ YES' : '❌ NO') . "\n";
