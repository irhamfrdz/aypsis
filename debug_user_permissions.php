<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "=== DEBUG USER PERMISSIONS ===\n\n";

// 1. Find user anggi
echo "1. Mencari user 'anggi':\n";
$user = User::where('username', 'anggi')->first();

if (!$user) {
    echo "❌ User 'anggi' tidak ditemukan!\n";
    echo "Available users:\n";
    $users = User::select('id', 'username', 'name')->get();
    foreach ($users as $u) {
        echo "- ID: {$u->id}, Username: {$u->username}, Name: {$u->name}\n";
    }
    exit;
}

echo "✅ User ditemukan: {$user->username} (ID: {$user->id})\n";
echo "📧 Email verified: " . ($user->email_verified_at ? 'YES' : 'NO') . "\n";
echo "📅 Email verified at: " . ($user->email_verified_at ?? 'NULL') . "\n\n";

// 2. Check specific permissions
echo "2. Checking specific permissions:\n";
$requiredPermissions = [
    'order-view',
    'order-create', 
    'order-update',
    'surat-jalan-view',
    'surat-jalan-create',
    'surat-jalan-update'
];

foreach ($requiredPermissions as $permission) {
    $hasPermission = $user->hasPermissionTo($permission);
    $status = $hasPermission ? '✅' : '❌';
    echo "{$status} {$permission}: " . ($hasPermission ? 'YES' : 'NO') . "\n";
}

echo "\n3. All permissions untuk user ini:\n";
$userPermissions = $user->getAllPermissions();
if ($userPermissions->count() > 0) {
    foreach ($userPermissions->take(20) as $perm) {
        echo "- {$perm->name}\n";
    }
    if ($userPermissions->count() > 20) {
        echo "... dan " . ($userPermissions->count() - 20) . " permission lainnya\n";
    }
} else {
    echo "❌ Tidak ada permission sama sekali!\n";
}

echo "\n4. User roles:\n";
$userRoles = $user->getRoleNames();
if ($userRoles->count() > 0) {
    foreach ($userRoles as $role) {
        echo "- {$role}\n";
    }
} else {
    echo "❌ Tidak ada role!\n";
}

echo "\n5. Check permission existence in database:\n";
foreach ($requiredPermissions as $permissionName) {
    $permission = Permission::where('name', $permissionName)->first();
    if ($permission) {
        echo "✅ Permission '{$permissionName}' exists in database\n";
    } else {
        echo "❌ Permission '{$permissionName}' NOT found in database!\n";
    }
}

echo "\n=== END DEBUG ===\n";

?>
