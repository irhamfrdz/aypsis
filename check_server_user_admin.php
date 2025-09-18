<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "=== SERVER: User Admin Role Check ===\n";

$user = User::where('username', 'user_admin')->first();

if ($user) {
    echo "✅ User found!\n";
    echo "ID: " . $user->id . "\n";
    echo "Username: " . $user->username . "\n";
    echo "Status: " . $user->status . "\n";

    // Check if user has hasRole method
    echo "\n🔍 Checking hasRole method:\n";
    echo "hasRole method exists: " . (method_exists($user, 'hasRole') ? '✅ Yes' : '❌ No') . "\n";

    if (method_exists($user, 'hasRole')) {
        $hasAdminRole = $user->hasRole('admin');
        echo "hasRole('admin'): " . ($hasAdminRole ? '✅ True' : '❌ False') . "\n";
    }

    // Check roles relationship
    echo "\n🔍 Checking roles relationship:\n";
    $rolesCount = $user->roles()->count();
    echo "Roles count: " . $rolesCount . "\n";

    if ($rolesCount > 0) {
        echo "User roles:\n";
        $roles = $user->roles()->get();
        foreach ($roles as $role) {
            echo "- ID: {$role->id}, Name: {$role->name}, Description: {$role->description}\n";
        }
    }

    // Check permissions
    $permissionCount = $user->permissions()->count();
    echo "\n🔍 Permissions:\n";
    echo "Permission count: " . $permissionCount . "\n";

    if ($permissionCount > 0) {
        echo "Sample permissions:\n";
        $permissions = $user->permissions()->take(5)->pluck('name')->toArray();
        foreach ($permissions as $perm) {
            echo "- " . $perm . "\n";
        }
    }

} else {
    echo "❌ User 'user_admin' not found!\n";

    // List all users
    echo "\nExisting users:\n";
    $users = User::select('id', 'username', 'status')->get();
    foreach ($users as $u) {
        echo "- ID: {$u->id}, Username: {$u->username}, Status: {$u->status}\n";
    }
}

// Check if Role model exists and has data
echo "\n🔍 Checking Role model:\n";
try {
    $roleCount = Role::count();
    echo "Role count: " . $roleCount . "\n";

    if ($roleCount > 0) {
        echo "Available roles:\n";
        $roles = Role::select('id', 'name', 'description')->get();
        foreach ($roles as $role) {
            echo "- ID: {$role->id}, Name: {$role->name}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking Role model: " . $e->getMessage() . "\n";
}
