<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING ALL PERBAIKAN KONTAINER PERMISSIONS ===\n\n";

// Permission yang dibutuhkan untuk sub menu perbaikan kontainer
$requiredPermissions = [
    // Perbaikan Kontainer dasar
    'perbaikan-kontainer.view' => 'Melihat daftar perbaikan kontainer',
    'perbaikan-kontainer.create' => 'Membuat perbaikan kontainer baru',
    'perbaikan-kontainer.update' => 'Mengupdate data perbaikan kontainer',
    'perbaikan-kontainer.delete' => 'Menghapus data perbaikan kontainer',
    'perbaikan-kontainer.print' => 'Mencetak data perbaikan kontainer',
    'perbaikan-kontainer.export' => 'Mengekspor data perbaikan kontainer',

    // Pranota Perbaikan Kontainer
    'pranota-perbaikan-kontainer.view' => 'Melihat daftar pranota perbaikan kontainer',
    'pranota-perbaikan-kontainer.create' => 'Membuat pranota perbaikan kontainer baru',
    'pranota-perbaikan-kontainer.update' => 'Mengupdate pranota perbaikan kontainer',
    'pranota-perbaikan-kontainer.delete' => 'Menghapus pranota perbaikan kontainer',
    'pranota-perbaikan-kontainer.print' => 'Mencetak pranota perbaikan kontainer',
    'pranota-perbaikan-kontainer.export' => 'Mengekspor pranota perbaikan kontainer',

    // Pembayaran Pranota Perbaikan Kontainer
    'pembayaran-pranota-perbaikan-kontainer.view' => 'Melihat daftar pembayaran pranota perbaikan kontainer',
    'pembayaran-pranota-perbaikan-kontainer.create' => 'Membuat pembayaran pranota perbaikan kontainer',
    'pembayaran-pranota-perbaikan-kontainer.update' => 'Mengupdate pembayaran pranota perbaikan kontainer',
    'pembayaran-pranota-perbaikan-kontainer.delete' => 'Menghapus pembayaran pranota perbaikan kontainer',
    'pembayaran-pranota-perbaikan-kontainer.print' => 'Mencetak pembayaran pranota perbaikan kontainer',
    'pembayaran-pranota-perbaikan-kontainer.export' => 'Mengekspor pembayaran pranota perbaikan kontainer',
];

$missingPermissions = [];
$existingPermissions = [];

echo "Checking permissions in database:\n";
echo str_repeat("-", 80) . "\n";

foreach ($requiredPermissions as $permissionName => $description) {
    $exists = DB::table('permissions')->where('name', $permissionName)->exists();

    if ($exists) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        echo "✅ EXISTS: {$permissionName} (ID: {$permission->id})\n";
        $existingPermissions[] = $permissionName;
    } else {
        echo "❌ MISSING: {$permissionName}\n";
        $missingPermissions[] = [
            'name' => $permissionName,
            'description' => $description
        ];
    }
}

echo "\n" . str_repeat("-", 80) . "\n";
echo "SUMMARY:\n";
echo "Total permissions checked: " . count($requiredPermissions) . "\n";
echo "Existing permissions: " . count($existingPermissions) . "\n";
echo "Missing permissions: " . count($missingPermissions) . "\n";

if (count($missingPermissions) > 0) {
    echo "\n❌ MISSING PERMISSIONS THAT NEED TO BE ADDED:\n";
    foreach ($missingPermissions as $missing) {
        echo "- {$missing['name']}: {$missing['description']}\n";
    }

    echo "\n🔧 Adding missing permissions...\n";

    $added = 0;
    foreach ($missingPermissions as $permission) {
        DB::table('permissions')->insert([
            'name' => $permission['name'],
            'description' => $permission['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Added: {$permission['name']}\n";
        $added++;
    }

    echo "\n✅ Successfully added {$added} missing permissions!\n";
} else {
    echo "\n✅ All required permissions are already in the database!\n";
}

echo "\n=== CHECKING USER ASSIGNMENTS ===\n";

// Check if any users have these permissions assigned
$userPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->whereIn('permissions.name', array_keys($requiredPermissions))
    ->select('user_permissions.user_id', 'permissions.name')
    ->get();

if (count($userPermissions) > 0) {
    echo "Users with perbaikan kontainer permissions:\n";
    $userGroups = [];
    foreach ($userPermissions as $up) {
        if (!isset($userGroups[$up->user_id])) {
            $userGroups[$up->user_id] = [];
        }
        $userGroups[$up->user_id][] = $up->name;
    }

    foreach ($userGroups as $userId => $permissions) {
        echo "- User ID {$userId}: " . count($permissions) . " permissions\n";
    }
} else {
    echo "❌ No users currently have these permissions assigned\n";
}

echo "\n=== PERMISSION CHECK COMPLETED ===\n";
