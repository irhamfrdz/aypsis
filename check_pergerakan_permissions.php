<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== Checking Pergerakan Kapal Permissions ===\n\n";

// 1. Check if permissions exist
echo "1. Checking permissions in database:\n";
$permissions = Permission::whereIn('name', [
    'pergerakan-kapal.view',
    'pergerakan-kapal.create',
    'pergerakan-kapal.edit',
    'pergerakan-kapal.delete'
])->get();

foreach($permissions as $perm) {
    echo "   ✓ {$perm->name} (ID: {$perm->id})\n";
}

if($permissions->count() === 0) {
    echo "   ❌ No pergerakan kapal permissions found!\n";
}

// 2. Check admin user
echo "\n2. Checking admin user:\n";
$admin = User::where('username', 'admin')->first();
if(!$admin) {
    $admin = User::where('role', 'admin')->first();
}

if($admin) {
    echo "   ✓ Admin user found: {$admin->username} (ID: {$admin->id})\n";
    echo "   ✓ Role: {$admin->role}\n";

    // 3. Check user permissions
    echo "\n3. Checking user permissions:\n";
    $userPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $admin->id)
        ->where('permissions.name', 'like', 'pergerakan-kapal%')
        ->select('permissions.name')
        ->get();

    if($userPermissions->count() > 0) {
        foreach($userPermissions as $perm) {
            echo "   ✓ {$perm->name}\n";
        }
    } else {
        echo "   ❌ No pergerakan kapal permissions assigned to admin!\n";

        // Auto-assign permissions
        echo "\n4. Auto-assigning permissions:\n";
        foreach($permissions as $permission) {
            $exists = DB::table('user_permissions')
                ->where('user_id', $admin->id)
                ->where('permission_id', $permission->id)
                ->exists();

            if(!$exists) {
                DB::table('user_permissions')->insert([
                    'user_id' => $admin->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "   ✓ Assigned {$permission->name}\n";
            }
        }
    }
} else {
    echo "   ❌ Admin user not found!\n";

    // Show available users
    echo "\n   Available users:\n";
    $users = User::take(5)->get();
    foreach($users as $user) {
        echo "   - {$user->username} (Role: {$user->role})\n";
    }
}

echo "\n=== Check Complete ===\n";
