<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

// Get admin user by username
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "Admin user not found!\n";
    exit(1);
}

echo "Admin user found: {$admin->name} (username: {$admin->username})\n";
echo "User ID: {$admin->id}\n\n";

// Get master kapal permissions
$permissions = Permission::where('name', 'like', 'master-kapal.%')->get();

echo "Master Kapal Permissions:\n";
foreach ($permissions as $perm) {
    echo "  - {$perm->name}: {$perm->description}\n";
}

echo "\n";

// Assign permissions to admin via user_permissions table
echo "Assigning permissions to admin...\n";
foreach ($permissions as $perm) {
    // Check if permission already assigned
    $exists = DB::table('user_permissions')
        ->where('user_id', $admin->id)
        ->where('permission_id', $perm->id)
        ->exists();
    
    if (!$exists) {
        DB::table('user_permissions')->insert([
            'user_id' => $admin->id,
            'permission_id' => $perm->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "  ✓ {$perm->name} assigned\n";
    } else {
        echo "  - {$perm->name} already assigned\n";
    }
}

echo "\nChecking permissions...\n";
$userPermissions = DB::table('user_permissions')
    ->where('user_id', $admin->id)
    ->whereIn('permission_id', $permissions->pluck('id'))
    ->count();

echo "Total Master Kapal permissions assigned: {$userPermissions}/{$permissions->count()}\n";

echo "\n✅ Done!\n";
