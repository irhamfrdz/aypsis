<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Assigning Pembayaran Pranota CAT permissions to admin role...\n";

$adminRole = DB::table('roles')->where('name', 'admin')->first();

if (!$adminRole) {
    echo "Admin role not found!\n";
    exit;
}

$permissions = [
    'pembayaran-pranota-cat.view',
    'pembayaran-pranota-cat.create',
    'pembayaran-pranota-cat.update',
    'pembayaran-pranota-cat.delete',
];

$assigned = 0;
$skipped = 0;

foreach ($permissions as $permissionName) {
    $permission = DB::table('permissions')->where('name', $permissionName)->first();

    if (!$permission) {
        echo "Permission not found: $permissionName\n";
        continue;
    }

    $exists = DB::table('role_permissions')
        ->where('role_id', $adminRole->id)
        ->where('permission_id', $permission->id)
        ->exists();

    if (!$exists) {
        DB::table('role_permissions')->insert([
            'role_id' => $adminRole->id,
            'permission_id' => $permission->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Assigned permission: {$permissionName} to admin\n";
        $assigned++;
    } else {
        echo "- Permission already assigned: {$permissionName}\n";
        $skipped++;
    }
}

echo "\nSummary: $assigned assigned, $skipped already assigned\n";
echo "Permission assignment completed!\n";
