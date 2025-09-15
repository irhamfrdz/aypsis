<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

echo "=== TESTING GATE::before LOGIC ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

// Test the Gate::before logic manually
echo "=== MANUAL Gate::before EXECUTION ===\n";

$ability = 'dashboard';
$userRoles = $user->roles();

try {
    // Check if user has admin role
    $hasAdminRole = $user->roles()->where('name', 'admin')->exists();
    echo "User has admin role: " . ($hasAdminRole ? '✅ YES' : '❌ NO') . "\n";

    if ($hasAdminRole) {
        echo "Gate::before should return true for admin user\n";
    }

    // Check hasPermissionLike
    $hasPermissionLike = method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike($ability);
    echo "hasPermissionLike('$ability'): " . ($hasPermissionLike ? '✅ YES' : '❌ NO') . "\n";

    // Check permission aliases
    $abilityAliases = config('permission_aliases', []);
    echo "Permission aliases defined: " . (isset($abilityAliases[$ability]) ? '✅ YES' : '❌ NO') . "\n";

    if (isset($abilityAliases[$ability])) {
        echo "Aliases for '$ability': " . json_encode($abilityAliases[$ability]) . "\n";
        foreach ($abilityAliases[$ability] as $prefix) {
            $hasPrefixLike = method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike($prefix);
            echo "hasPermissionLike('$prefix'): " . ($hasPrefixLike ? '✅ YES' : '❌ NO') . "\n";

            $hasPrefixMatch = method_exists($user, 'hasPermissionMatch') && $user->hasPermissionMatch($prefix);
            echo "hasPermissionMatch('$prefix'): " . ($hasPrefixMatch ? '✅ YES' : '❌ NO') . "\n";
        }
    }

    // Check heuristic matching
    $hasPermissionMatch = method_exists($user, 'hasPermissionMatch') && $user->hasPermissionMatch($ability);
    echo "hasPermissionMatch('$ability'): " . ($hasPermissionMatch ? '✅ YES' : '❌ NO') . "\n";

} catch (Exception $e) {
    echo "❌ Exception in Gate::before logic: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test if the issue is with the Gate facade itself
echo "\n=== TESTING GATE FACADE DIRECTLY ===\n";

try {
    // Create a completely isolated gate
    Gate::define('isolated-test', function () {
        return true;
    });

    // Test without passing user
    $isolatedResult = Gate::check('isolated-test');
    echo "Isolated gate (no user): " . ($isolatedResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

    // Test with user
    $isolatedWithUserResult = Gate::check('isolated-test', [$user]);
    echo "Isolated gate (with user): " . ($isolatedWithUserResult ? '✅ ALLOWED' : '❌ DENIED') . "\n";

} catch (Exception $e) {
    echo "❌ Exception in isolated gate test: " . $e->getMessage() . "\n";
}
