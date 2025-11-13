<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use App\Models\User;
use App\Models\Role;

echo "Menghapus permissions OB Bongkar...\n\n";

$permissionNames = [
    'ob-bongkar-view',
    'ob-bongkar-create',
    'ob-bongkar-edit',
    'ob-bongkar-delete'
];

$permissions = Permission::whereIn('name', $permissionNames)->get();

if ($permissions->isEmpty()) {
    echo "Tidak ada permissions OB Bongkar yang ditemukan.\n";
    exit;
}

echo "Permissions yang akan dihapus:\n";
foreach ($permissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}
echo "\n";

$totalUsersDetached = 0;
$totalRolesDetached = 0;

// Detach from all users
foreach ($permissions as $permission) {
    $users = User::whereHas('permissions', function($q) use ($permission) {
        $q->where('permissions.id', $permission->id);
    })->get();
    
    foreach ($users as $user) {
        $user->permissions()->detach($permission->id);
        $totalUsersDetached++;
    }
}

// Detach from all roles
foreach ($permissions as $permission) {
    $roles = Role::whereHas('permissions', function($q) use ($permission) {
        $q->where('permissions.id', $permission->id);
    })->get();
    
    foreach ($roles as $role) {
        $role->permissions()->detach($permission->id);
        $totalRolesDetached++;
    }
}

echo "Dilepas dari users: {$totalUsersDetached} relasi\n";
echo "Dilepas dari roles: {$totalRolesDetached} relasi\n";

// Delete permissions
$deletedCount = 0;
foreach ($permissions as $permission) {
    $permission->delete();
    $deletedCount++;
}

echo "Dihapus dari permissions: {$deletedCount} record\n";

echo "\n✅ Semua permissions OB Bongkar berhasil dihapus!\n";
