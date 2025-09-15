<?php

require_once 'vendor/autoload.php';

use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Verifying Admin User Access ===\n\n";

// Get admin user
$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "Admin user: {$user->username} (ID: {$user->id})\n";
echo "Total permissions: {$user->permissions->count()}\n\n";

// Test key permissions
$keyPermissions = [
    'master-user',
    'tagihan-kontainer-view',
    'tagihan-kontainer-create',
    'tagihan-kontainer-update',
    'pranota.index',
    'pembayaran-pranota-kontainer.index',
    'dashboard',
    'master-karyawan',
    'master-kontainer',
    'permohonan.index'
];

echo "Testing key permissions:\n";
$allPassed = true;

foreach ($keyPermissions as $permission) {
    $hasPermission = $user->can($permission);
    $status = $hasPermission ? '✅' : '❌';
    echo "  $status $permission\n";

    if (!$hasPermission) {
        $allPassed = false;
    }
}

echo "\n";

if ($allPassed) {
    echo "🎉 All key permissions verified! Admin has full access.\n";
} else {
    echo "⚠️  Some permissions may be missing. Please check.\n";
}

// Test admin role check (if applicable)
echo "\nAdmin role check:\n";
if (method_exists($user, 'hasRole')) {
    $isAdmin = $user->hasRole('admin') || $user->hasRole('super-admin');
    echo "  " . ($isAdmin ? '✅' : '❌') . " Has admin/super-admin role\n";
} else {
    echo "  ℹ️  Role system not available or not configured\n";
}

echo "\n✅ Admin user verification complete!\n";
echo "The admin user should now have access to all features in the system.\n";
