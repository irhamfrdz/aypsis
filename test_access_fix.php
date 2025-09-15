<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing access to karyawan routes for user test4\n";
echo "==================================================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n\n";

// Simulate authentication
Auth::login($user);

$testRoutes = [
    'master.karyawan.index' => 'can:master-karyawan.view',
    'master.karyawan.create' => 'can:master-karyawan.create',
    'master.karyawan.show' => 'can:master-karyawan.view',
    'master.karyawan.edit' => 'can:master-karyawan.update',
    'master.karyawan.store' => 'can:master-karyawan.create',
    'master.karyawan.update' => 'can:master-karyawan.update',
    'master.karyawan.destroy' => 'can:master-karyawan.delete',
];

echo "Testing route access:\n";
foreach ($testRoutes as $routeName => $middleware) {
    try {
        // Extract permission from middleware string
        $permission = str_replace('can:', '', $middleware);

        // Check if user can access this permission
        $canAccess = $user->hasPermissionTo($permission);

        echo "  - Route: $routeName ($permission): " . ($canAccess ? '✅ ALLOWED' : '❌ BLOCKED') . "\n";
    } catch (Exception $e) {
        echo "  - Route: $routeName: ❌ ERROR - {$e->getMessage()}\n";
    }
}

echo "\n📋 Summary:\n";
$hasAnyKaryawanPermission = $user->hasPermissionTo('master-karyawan.view') ||
                          $user->hasPermissionTo('master-karyawan.create') ||
                          $user->hasPermissionTo('master-karyawan.update') ||
                          $user->hasPermissionTo('master-karyawan.delete');

echo "User test4 has any karyawan permissions: " . ($hasAnyKaryawanPermission ? '✅ YES' : '❌ NO') . "\n";
echo "User test4 can access karyawan data: " . ($hasAnyKaryawanPermission ? '❌ YES (PROBLEM!)' : '✅ NO (FIXED!)') . "\n\n";

echo "🎯 Test completed!\n";
