<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ASSIGNING PERBAIKAN KONTAINER PERMISSIONS TO ADMIN ===\n\n";

// Get admin user (assuming user with role admin or user ID 1)
$adminUser = DB::table('users')->where('id', 1)->first();

if (!$adminUser) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "Admin user found: {$adminUser->username} (ID: {$adminUser->id})\n\n";

// Permission yang baru ditambahkan
$newPermissions = [
    'perbaikan-kontainer.print',
    'perbaikan-kontainer.export',
    'pranota-perbaikan-kontainer.view',
    'pranota-perbaikan-kontainer.create',
    'pranota-perbaikan-kontainer.update',
    'pranota-perbaikan-kontainer.delete',
    'pranota-perbaikan-kontainer.print',
    'pranota-perbaikan-kontainer.export',
    'pembayaran-pranota-perbaikan-kontainer.view',
    'pembayaran-pranota-perbaikan-kontainer.create',
    'pembayaran-pranota-perbaikan-kontainer.update',
    'pembayaran-pranota-perbaikan-kontainer.delete',
    'pembayaran-pranota-perbaikan-kontainer.print',
    'pembayaran-pranota-perbaikan-kontainer.export',
];

$assigned = 0;
$skipped = 0;

echo "Assigning permissions to admin user:\n";
echo str_repeat("-", 50) . "\n";

foreach ($newPermissions as $permissionName) {
    $permission = DB::table('permissions')->where('name', $permissionName)->first();

    if (!$permission) {
        echo "❌ Permission not found: {$permissionName}\n";
        continue;
    }

    // Check if user already has this permission
    $exists = DB::table('user_permissions')
        ->where('user_id', $adminUser->id)
        ->where('permission_id', $permission->id)
        ->exists();

    if ($exists) {
        echo "⏭️  Already assigned: {$permissionName}\n";
        $skipped++;
    } else {
        DB::table('user_permissions')->insert([
            'user_id' => $adminUser->id,
            'permission_id' => $permission->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Assigned: {$permissionName}\n";
        $assigned++;
    }
}

echo "\n" . str_repeat("-", 50) . "\n";
echo "SUMMARY:\n";
echo "Total permissions processed: " . count($newPermissions) . "\n";
echo "Newly assigned: {$assigned}\n";
echo "Already assigned: {$skipped}\n";

if ($assigned > 0) {
    echo "\n✅ Successfully assigned {$assigned} new permissions to admin user!\n";
} else {
    echo "\nℹ️  All permissions were already assigned to admin user.\n";
}

// Verify assignment
echo "\n=== VERIFICATION ===\n";
$totalPermissions = DB::table('user_permissions')
    ->where('user_id', $adminUser->id)
    ->count();

$perbaikanPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $adminUser->id)
    ->where('permissions.name', 'like', '%perbaikan-kontainer%')
    ->count();

echo "Admin user total permissions: {$totalPermissions}\n";
echo "Admin user perbaikan kontainer permissions: {$perbaikanPermissions}\n";

echo "\n=== ASSIGNMENT COMPLETED ===\n";
