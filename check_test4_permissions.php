<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking user test4 permissions\n";
echo "===============================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Check if user has admin role
$hasAdminRole = $user->hasRole('admin');
echo "Has admin role: " . ($hasAdminRole ? '✅ YES' : '❌ NO') . "\n\n";

// Get current permissions
$permissions = $user->permissions;
echo "Current permissions in database:\n";
if ($permissions->count() === 0) {
    echo "❌ No permissions found!\n";
} else {
    foreach ($permissions as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
}
echo "\n";

// Check specific permissions
$checkPermissions = [
    'master-karyawan',
    'master-karyawan.view',
    'master-karyawan.create',
    'master-karyawan.update',
    'master-karyawan.delete'
];

echo "Checking specific permissions:\n";
foreach ($checkPermissions as $permName) {
    $hasPermission = $user->hasPermissionTo($permName);
    echo "  - $permName: " . ($hasPermission ? '✅ HAS' : '❌ MISSING') . "\n";
}

echo "\nTest completed!\n";
