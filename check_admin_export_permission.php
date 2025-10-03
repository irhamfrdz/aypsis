<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "=== Check Admin Permissions ===\n\n";

// Find admin user
$admin = User::where('username', 'admin')
    ->orWhere('username', 'LIKE', '%admin%')
    ->first();

if (!$admin) {
    echo "❌ Admin user not found!\n";
    echo "Available users:\n";
    $users = User::limit(10)->get();
    foreach ($users as $user) {
        echo "  - ID: {$user->id}, Username: {$user->username}, Status: {$user->status}\n";
    }
    exit(1);
}

echo "✓ Found admin user: {$admin->username} (ID: {$admin->id})\n\n";

// Check roles
echo "=== Admin Roles ===\n";
$roles = $admin->roles;
if ($roles->isEmpty()) {
    echo "❌ No roles assigned!\n\n";
} else {
    foreach ($roles as $role) {
        echo "✓ {$role->name}\n";
    }
    echo "\n";
}

// Check all permissions related to tagihan-kontainer
echo "=== Tagihan Kontainer Permissions in Database ===\n";
$tagihanPermissions = DB::table('permissions')->where('name', 'LIKE', '%tagihan-kontainer%')->get();

if ($tagihanPermissions->isEmpty()) {
    echo "❌ No tagihan-kontainer permissions found in database!\n\n";
} else {
    foreach ($tagihanPermissions as $perm) {
        echo "  - {$perm->name}\n";
    }
    echo "\n";
}

// Check specific permissions for admin
echo "=== Admin's Tagihan Kontainer Permissions ===\n";
$hasAnyTagihanPerm = false;
foreach ($tagihanPermissions as $perm) {
    if ($admin->hasPermissionTo($perm->name)) {
        echo "✓ {$perm->name}\n";
        $hasAnyTagihanPerm = true;
    }
}

if (!$hasAnyTagihanPerm) {
    echo "❌ Admin doesn't have any tagihan-kontainer permissions!\n";
}
echo "\n";

// Check specific permission needed for export
$permissionsToCheck = [
    'tagihan-kontainer-sewa-view',
    'tagihan-kontainer-sewa-create',
    'tagihan-kontainer-create',
    'tagihan-kontainer-view',
];

echo "=== Check Specific Permissions ===\n";
foreach ($permissionsToCheck as $permName) {
    $hasPermission = $admin->hasPermissionTo($permName);
    $exists = DB::table('permissions')->where('name', $permName)->exists();

    if ($hasPermission) {
        echo "✓ Admin HAS '{$permName}'\n";
    } else {
        echo "❌ Admin DOESN'T HAVE '{$permName}'";
        if (!$exists) {
            echo " (permission doesn't exist in database)";
        }
        echo "\n";
    }
}

echo "\n=== Recommendation ===\n";
if (!$admin->hasPermissionTo('tagihan-kontainer-sewa-create')) {
    echo "❌ Admin needs 'tagihan-kontainer-sewa-create' permission to see Export button\n\n";

    // Check if permission exists
    $permission = DB::table('permissions')->where('name', 'tagihan-kontainer-sewa-create')->first();

    if ($permission) {
        echo "Permission exists. You can assign it with:\n";
        echo "  \$admin->givePermissionTo('tagihan-kontainer-sewa-create');\n\n";
    } else {
        echo "Permission doesn't exist. You need to create it first:\n";
        echo "  Permission::create(['name' => 'tagihan-kontainer-sewa-create']);\n";
        echo "  \$admin->givePermissionTo('tagihan-kontainer-sewa-create');\n\n";
    }
} else {
    echo "✓ Admin has the required permission!\n";
    echo "✓ Export button should be visible.\n\n";
}

echo "=== Test Complete ===\n";
