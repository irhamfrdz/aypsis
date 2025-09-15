<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "🧪 Testing Permission-Like Middleware for User test2\n";
echo "==================================================\n\n";

// Find user test2
$user = User::where('username', 'test2')->first();

if (!$user) {
    echo "❌ User test2 not found\n";
    exit(1);
}

echo "✅ Found user: {$user->username} (ID: {$user->id})\n\n";

// Get user permissions
$userPermissions = $user->permissions->pluck('name')->toArray();
echo "📋 Current permissions for user test2:\n";
foreach ($userPermissions as $perm) {
    echo "  - {$perm}\n";
}
echo "\n";

// Test permission-like checking
echo "🔍 Testing permission-like checks:\n";

$testPrefixes = [
    'pranota-supir',
    'pembayaran-pranota-supir',
    'master-pranota-supir',
    'nonexistent'
];

foreach ($testPrefixes as $prefix) {
    $hasPermission = $user->hasPermissionLike($prefix);
    $status = $hasPermission ? '✅ ALLOWED' : '❌ DENIED';
    echo "  {$prefix}: {$status}\n";
}

echo "\n";

// Test route access simulation
echo "🚪 Route Access Simulation:\n";

$routes = [
    'pranota-supir.index' => 'pranota-supir',
    'pranota-supir.create' => 'pranota-supir',
    'pranota-supir.show' => 'pranota-supir',
    'pranota-supir.print' => 'pranota-supir',
    'pranota-supir.store' => 'pranota-supir',
    'pembayaran-pranota-supir.create' => 'pembayaran-pranota-supir',
    'pembayaran-pranota-supir.print' => 'pembayaran-pranota-supir',
];

foreach ($routes as $routeName => $requiredPrefix) {
    $hasAccess = $user->hasPermissionLike($requiredPrefix);
    $status = $hasAccess ? '✅ ACCESSIBLE' : '❌ BLOCKED';
    echo "  {$routeName}: {$status}\n";
}

echo "\n🎉 Test completed!\n";

if ($user->hasPermissionLike('pranota-supir')) {
    echo "✅ SUCCESS: User test2 should now be able to access pranota pages!\n";
} else {
    echo "❌ FAILURE: User test2 still cannot access pranota pages\n";
}
