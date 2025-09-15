<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== TESTING GATE CALLBACK EXECUTION ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

// Manually execute the gate callback logic
echo "=== MANUAL GATE CALLBACK EXECUTION ===\n";

try {
    // This is the exact logic from AuthServiceProvider
    $permissions = Permission::all();
    $dashboardPermission = $permissions->where('name', 'dashboard')->first();

    if ($dashboardPermission) {
        echo "Found dashboard permission: {$dashboardPermission->name} (ID: {$dashboardPermission->id})\n";

        // Execute the callback logic manually
        $callbackResult = $user->hasPermissionTo($dashboardPermission->name);
        echo "Manual callback execution result: " . ($callbackResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";
    } else {
        echo "❌ Dashboard permission not found\n";
    }
} catch (Exception $e) {
    echo "❌ Exception in manual callback: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test if the issue is with the Gate facade
echo "\n=== TESTING GATE FACADE ===\n";

try {
    // Test a simple gate definition
    Gate::define('test-permission', function (User $user) {
        return true; // Always return true
    });

    $testResult = Gate::check('test-permission', $user);
    echo "Simple test gate result: " . ($testResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

    // Test with the actual permission logic
    Gate::define('test-dashboard', function (User $user) {
        return $user->hasPermissionTo('dashboard');
    });

    $testDashboardResult = Gate::check('test-dashboard', $user);
    echo "Test dashboard gate result: " . ($testDashboardResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

} catch (Exception $e) {
    echo "❌ Exception in gate facade test: " . $e->getMessage() . "\n";
}
