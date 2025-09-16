<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "🔍 DEBUGGING PERMISSION FOR USER TEST4\n";
echo "=======================================\n\n";

// Cari user test4
$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "❌ User test4 tidak ditemukan!\n";
    exit(1);
}

echo "👤 User Info:\n";
echo "- ID: {$user->id}\n";
echo "- Username: {$user->username}\n";
echo "- Name: {$user->name}\n";
echo "- Status: {$user->status}\n\n";

// Cek permission master-karyawan.view
$hasPermission = $user->can('master-karyawan.view');
echo "🔐 Permission Check:\n";
echo "- master-karyawan.view: " . ($hasPermission ? '✅ GRANTED' : '❌ DENIED') . "\n";

// Cek semua permission yang dimiliki user
echo "\n📋 All User Permissions:\n";
try {
    $permissions = DB::table('permissions')
        ->join('user_permissions', 'permissions.id', '=', 'user_permissions.permission_id')
        ->where('user_permissions.user_id', $user->id)
        ->select('permissions.name')
        ->get();

    if ($permissions->count() > 0) {
        foreach ($permissions as $permission) {
            echo "- {$permission->name}\n";
        }
    } else {
        echo "- ❌ No permissions found!\n";
    }
} catch (Exception $e) {
    echo "- Error checking permissions: {$e->getMessage()}\n";
}

// Cek roles
echo "\n👥 User Roles:\n";
try {
    $roles = DB::table('roles')
        ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
        ->where('user_roles.user_id', $user->id)
        ->select('roles.name')
        ->get();

    if ($roles->count() > 0) {
        foreach ($roles as $role) {
            echo "- {$role->name}\n";
        }
    } else {
        echo "- ❌ No roles assigned!\n";
    }
} catch (Exception $e) {
    echo "- Error checking roles: {$e->getMessage()}\n";
}

// Cek permission matrix dari database
echo "\n🗄️  Permission Matrix Check:\n";
$matrixPermissions = DB::table('user_permissions')
    ->where('user_id', $user->id)
    ->where('module', 'master-karyawan')
    ->get();

if ($matrixPermissions->count() > 0) {
    foreach ($matrixPermissions as $perm) {
        echo "- Module: {$perm->module}, Action: {$perm->action}, Value: {$perm->value}\n";
    }
} else {
    echo "- ❌ No matrix permissions found for master-karyawan!\n";
}

// Test route access
echo "\n🛣️  Route Access Test:\n";
try {
    $routeExists = app('router')->getRoutes()->getByName('master.karyawan.index');
    if ($routeExists) {
        echo "- Route master.karyawan.index: ✅ EXISTS\n";
        echo "- Route URI: {$routeExists->uri()}\n";
        echo "- Route Methods: " . implode(', ', $routeExists->methods()) . "\n";

        // Check middleware
        $middleware = $routeExists->middleware();
        if (!empty($middleware)) {
            echo "- Route Middleware: " . implode(', ', $middleware) . "\n";
        } else {
            echo "- Route Middleware: None\n";
        }
    } else {
        echo "- Route master.karyawan.index: ❌ NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "- Route check error: {$e->getMessage()}\n";
}

echo "\n🎯 CONCLUSION:\n";
if ($hasPermission) {
    echo "✅ User should be able to see Master Karyawan menu in sidebar\n";
} else {
    echo "❌ User does NOT have permission to view Master Karyawan menu\n";
    echo "💡 Possible solutions:\n";
    echo "   1. Check if permission is properly assigned in user edit form\n";
    echo "   2. Clear cache: php artisan cache:clear\n";
    echo "   3. Clear permission cache: php artisan permission:cache-reset\n";
    echo "   4. Check database user_permissions table\n";
}
