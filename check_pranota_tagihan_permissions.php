<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔍 Checking User test2 Permissions for Pranota Tagihan Kontainer\n";
echo "=============================================================\n\n";

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

// Filter permissions related to pranota tagihan kontainer
$pranotaTagihanPermissions = array_filter($userPermissions, function($perm) {
    return strpos($perm, 'pranota-tagihan-kontainer') !== false;
});

echo "🎯 Pranota Tagihan Kontainer related permissions:\n";
if (empty($pranotaTagihanPermissions)) {
    echo "  ❌ No pranota-tagihan-kontainer permissions found\n";
} else {
    foreach ($pranotaTagihanPermissions as $perm) {
        echo "  ✅ {$perm}\n";
    }
}
echo "\n";

// Test permission-like checking for different prefixes
echo "🔍 Testing permission-like checks:\n";

$testPrefixes = [
    'pranota-tagihan-kontainer',
    'master-pranota-tagihan-kontainer',
    'pranota-tagihan',
    'tagihan-kontainer'
];

foreach ($testPrefixes as $prefix) {
    $hasPermission = $user->hasPermissionLike($prefix);
    $status = $hasPermission ? '✅ ALLOWED' : '❌ DENIED';
    echo "  {$prefix}: {$status}\n";
}

echo "\n🎉 Permission analysis completed!\n";
