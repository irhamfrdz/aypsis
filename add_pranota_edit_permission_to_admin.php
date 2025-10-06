<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Adding pranota-kontainer-sewa-edit Permission to Admin User ===\n";

// Get admin user
$user = DB::table('users')->where('username', 'admin')->first();
if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "User: {$user->username} (ID: {$user->id})\n";

// Check if permission exists
$permission = DB::table('permissions')->where('name', 'pranota-kontainer-sewa-edit')->first();
if (!$permission) {
    echo "❌ Permission 'pranota-kontainer-sewa-edit' not found in database!\n";
    exit(1);
}

echo "✓ Permission found: {$permission->name} (ID: {$permission->id})\n";

// Check if user already has this permission
$hasPermission = DB::table('user_permissions')
    ->where('user_id', $user->id)
    ->where('permission_id', $permission->id)
    ->exists();

if ($hasPermission) {
    echo "✓ User already has this permission!\n";
} else {
    // Add permission to user
    DB::table('user_permissions')->insert([
        'user_id' => $user->id,
        'permission_id' => $permission->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✅ Permission 'pranota-kontainer-sewa-edit' added to user admin!\n";
}

echo "\n=== Done ===\n";
