<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "🧪 Testing Middleware Logic for User test2\n";
echo "=========================================\n\n";

// Login as user test2
$user = User::where('username', 'test2')->first();

if (!$user) {
    echo "❌ User test2 not found\n";
    exit(1);
}

Auth::login($user);
echo "✅ Logged in as: {$user->username}\n\n";

// Test the middleware logic directly
echo "🔍 Testing middleware permission check:\n";

$requiredPermission = 'pranota-supir';

// Test hasPermissionLike method
$hasPermissionLike = $user->hasPermissionLike($requiredPermission);
echo "  hasPermissionLike('{$requiredPermission}'): " . ($hasPermissionLike ? '✅ TRUE' : '❌ FALSE') . "\n";

// Test hasPermissionTo method (exact match)
$hasPermissionTo = $user->hasPermissionTo($requiredPermission);
echo "  hasPermissionTo('{$requiredPermission}'): " . ($hasPermissionTo ? '✅ TRUE' : '❌ FALSE') . "\n";

// Test hasPermissionTo with master permission
$masterPermission = 'master-pranota-supir';
$hasMasterPermission = $user->hasPermissionTo($masterPermission);
echo "  hasPermissionTo('{$masterPermission}'): " . ($hasMasterPermission ? '✅ TRUE' : '❌ FALSE') . "\n\n";

// Show user's permissions
echo "📋 User's permissions:\n";
foreach ($user->permissions as $permission) {
    echo "  - {$permission->name}\n";
}

echo "\n";

// Test middleware simulation
echo "🛡️ Middleware Simulation:\n";

if ($hasPermissionLike) {
    echo "  ✅ Permission-like middleware would ALLOW access\n";
    echo "  ✅ User test2 should be able to access pranota pages\n";
} else {
    echo "  ❌ Permission-like middleware would BLOCK access\n";
    echo "  ❌ User test2 would still get 403 error\n";
}

echo "\n🎉 Middleware test completed!\n";

if ($hasPermissionLike) {
    echo "✅ SUCCESS: The permission-like middleware fix should resolve the 403 error!\n";
} else {
    echo "❌ FAILURE: The middleware fix is not working as expected\n";
}
