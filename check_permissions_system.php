<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=== ANALISIS SISTEM PERMISSION ===\n\n";

// 1. Cek total permissions
$totalPermissions = Permission::count();
echo "1. Total Permissions: {$totalPermissions}\n\n";

// 2. Cek permissions terkait user
echo "2. Permissions terkait User Management:\n";
$userPermissions = Permission::where('name', 'like', '%user%')
    ->orWhere('name', 'like', '%master.user%')
    ->get();

foreach ($userPermissions as $perm) {
    echo "   - {$perm->name} ({$perm->description})\n";
}

// 3. Cek permissions master
echo "\n3. Permissions Master:\n";
$masterPermissions = Permission::where('name', 'like', '%master%')
    ->orderBy('name')
    ->take(20)
    ->get();

foreach ($masterPermissions as $perm) {
    echo "   - {$perm->name} ({$perm->description})\n";
}

// 4. Cek struktur tabel user_permissions
echo "\n4. Cek tabel user_permissions:\n";
try {
    $userPermissionCount = \DB::table('user_permissions')->count();
    echo "   Total user-permission assignments: {$userPermissionCount}\n";

    // Cek contoh assignment
    $samples = \DB::table('user_permissions')
        ->join('users', 'user_permissions.user_id', '=', 'users.id')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->select('users.name as user_name', 'permissions.name as permission_name')
        ->take(10)
        ->get();

    echo "   Contoh assignments:\n";
    foreach ($samples as $sample) {
        echo "     * {$sample->user_name} -> {$sample->permission_name}\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// 5. Test relasi User-Permission
echo "\n5. Test Model Relationships:\n";
try {
    $user = User::with('permissions')->first();
    if ($user) {
        echo "   User: {$user->name}\n";
        echo "   Total permissions: " . $user->permissions->count() . "\n";

        if ($user->permissions->count() > 0) {
            echo "   First 5 permissions:\n";
            foreach ($user->permissions->take(5) as $perm) {
                echo "     - {$perm->name}\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
