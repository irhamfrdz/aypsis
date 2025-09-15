<?php

// Test script to check user test4 permissions for pembayaran-pranota-kontainer
require_once 'vendor/autoload.php';

use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Checking User test4 Permissions for Pembayaran Pranota Kontainer\n";
echo "=================================================================\n\n";

$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "❌ FAIL: User test4 not found\n";
    exit(1);
}

echo "✅ User test4 found (ID: {$user->id})\n\n";

// Check pembayaran-pranota-kontainer permissions
$pembayaranPermissions = [
    'pembayaran-pranota-kontainer.view' => $user->hasPermissionTo('pembayaran-pranota-kontainer.view'),
    'pembayaran-pranota-kontainer.create' => $user->hasPermissionTo('pembayaran-pranota-kontainer.create'),
    'pembayaran-pranota-kontainer.update' => $user->hasPermissionTo('pembayaran-pranota-kontainer.update'),
    'pembayaran-pranota-kontainer.delete' => $user->hasPermissionTo('pembayaran-pranota-kontainer.delete'),
    'pembayaran-pranota-kontainer.print' => $user->hasPermissionTo('pembayaran-pranota-kontainer.print'),
];

echo "Pembayaran Pranota Kontainer permissions for test4:\n";
foreach ($pembayaranPermissions as $permission => $hasPermission) {
    $status = $hasPermission ? '✅ YES' : '❌ NO';
    echo "  {$permission}: {$status}\n";
}

echo "\n📋 ANALYSIS:\n";
$hasAnyPembayaranPermission = array_sum($pembayaranPermissions) > 0;

if (!$hasAnyPembayaranPermission) {
    echo "❌ User test4 has NO permissions for pembayaran-pranota-kontainer\n";
    echo "🔒 This means user test4 should be BLOCKED from accessing ANY pembayaran-pranota-kontainer features\n";
    echo "🚨 SECURITY ISSUE: Routes are not protected with middleware!\n";
} else {
    echo "⚠️ User test4 has some pembayaran-pranota-kontainer permissions\n";
    echo "📝 Need to check which specific permissions they have\n";
}

echo "\n🔧 RECOMMENDATION:\n";
echo "Add middleware protection to pembayaran-pranota-kontainer routes:\n";
echo "- index route: should require view permission\n";
echo "- create route: should require create permission\n";
echo "- store route: should require create permission\n";
echo "- show route: should require view permission\n";
echo "- edit route: should require update permission\n";
echo "- update route: should require update permission\n";
echo "- destroy route: should require delete permission\n";

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
