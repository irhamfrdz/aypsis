<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING 404 ISSUE ===" . PHP_EOL;

// Test 1: Check if we can access basic routes
echo "1. Testing basic routes..." . PHP_EOL;
try {
    $basicUrl = route('login');
    echo "✓ Login route works: $basicUrl" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Login route failed: " . $e->getMessage() . PHP_EOL;
}

// Test 2: Check approval route specifically
echo PHP_EOL . "2. Testing approval route..." . PHP_EOL;
try {
    $approvalUrl = route('approval.surat-jalan.index');
    echo "✓ Approval route exists: $approvalUrl" . PHP_EOL;
} catch (Exception $e) {
    echo "✗ Approval route failed: " . $e->getMessage() . PHP_EOL;
}

// Test 3: Check if route is actually registered
echo PHP_EOL . "3. Checking route registration..." . PHP_EOL;
$router = app('router');
$found = false;
foreach ($router->getRoutes() as $route) {
    if ($route->getName() === 'approval.surat-jalan.index') {
        $found = true;
        echo "✓ Route found in router" . PHP_EOL;
        echo "  URI: " . $route->uri() . PHP_EOL;
        echo "  Methods: " . implode(', ', $route->methods()) . PHP_EOL;
        break;
    }
}
if (!$found) {
    echo "✗ Route NOT found in router!" . PHP_EOL;
}

// Test 4: Check middleware chain
echo PHP_EOL . "4. Testing middleware access..." . PHP_EOL;
$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    echo "✓ Admin user found" . PHP_EOL;

    // Login user
    \Illuminate\Support\Facades\Auth::login($user);

    // Check each permission
    $permissions = [
        'surat-jalan-approval-dashboard',
        'surat-jalan-approval-level-1-view',
        'surat-jalan-approval-level-2-view'
    ];

    foreach ($permissions as $permission) {
        $has = $user->can($permission);
        echo "  $permission: " . ($has ? "✓" : "✗") . PHP_EOL;
    }
} else {
    echo "✗ Admin user not found!" . PHP_EOL;
}

echo PHP_EOL . "=== END DEBUG ===" . PHP_EOL;
