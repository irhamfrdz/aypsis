<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=== Adding Pranota Rit & Pranota Rit Kenek Permissions ===\n";

// Define pranota-rit permissions
$pranotaRitPermissions = [
    [
        'name' => 'pranota-rit-view',
        'description' => 'Lihat data pranota rit'
    ],
    [
        'name' => 'pranota-rit-create',
        'description' => 'Tambah pranota rit baru'
    ],
    [
        'name' => 'pranota-rit-edit',
        'description' => 'Edit data pranota rit'
    ],
    [
        'name' => 'pranota-rit-update',
        'description' => 'Update data pranota rit'
    ],
    [
        'name' => 'pranota-rit-delete',
        'description' => 'Hapus data pranota rit'
    ],
    [
        'name' => 'pranota-rit-print',
        'description' => 'Print dokumen pranota rit'
    ],
    [
        'name' => 'pranota-rit-export',
        'description' => 'Export pranota rit ke Excel'
    ],
    [
        'name' => 'pranota-rit-approve',
        'description' => 'Approve pranota rit'
    ]
];

// Define pranota-rit-kenek permissions
$pranotaRitKenekPermissions = [
    [
        'name' => 'pranota-rit-kenek-view',
        'description' => 'Lihat data pranota rit kenek'
    ],
    [
        'name' => 'pranota-rit-kenek-create',
        'description' => 'Tambah pranota rit kenek baru'
    ],
    [
        'name' => 'pranota-rit-kenek-edit',
        'description' => 'Edit data pranota rit kenek'
    ],
    [
        'name' => 'pranota-rit-kenek-update',
        'description' => 'Update data pranota rit kenek'
    ],
    [
        'name' => 'pranota-rit-kenek-delete',
        'description' => 'Hapus data pranota rit kenek'
    ],
    [
        'name' => 'pranota-rit-kenek-print',
        'description' => 'Print dokumen pranota rit kenek'
    ],
    [
        'name' => 'pranota-rit-kenek-export',
        'description' => 'Export pranota rit kenek ke Excel'
    ],
    [
        'name' => 'pranota-rit-kenek-approve',
        'description' => 'Approve pranota rit kenek'
    ]
];

$allPermissions = array_merge($pranotaRitPermissions, $pranotaRitKenekPermissions);

echo "\n🔍 Checking existing permissions...\n";

$addedCount = 0;
$existingCount = 0;
$addedPermissionIds = [];

foreach ($allPermissions as $permData) {
    $existing = Permission::where('name', $permData['name'])->first();
    
    if (!$existing) {
        $permission = Permission::create([
            'name' => $permData['name'],
            'description' => $permData['description']
        ]);
        
        $addedPermissionIds[] = $permission->id;
        echo "✅ Added: {$permData['name']} (ID: {$permission->id})\n";
        $addedCount++;
    } else {
        echo "→ Exists: {$permData['name']} (ID: {$existing->id})\n";
        $existingCount++;
    }
}

echo "\n📊 Summary:\n";
echo "   • Added: {$addedCount} new permissions\n";
echo "   • Existing: {$existingCount} permissions\n";
echo "   • Total Pranota Rit permissions: " . count($pranotaRitPermissions) . "\n";
echo "   • Total Pranota Rit Kenek permissions: " . count($pranotaRitKenekPermissions) . "\n";

// Add new permissions to admin user
if (!empty($addedPermissionIds)) {
    echo "\n👤 Adding new permissions to admin user...\n";
    
    $admin = User::where('username', 'admin')->first();
    if ($admin) {
        $admin->permissions()->attach($addedPermissionIds);
        $totalAdminPerms = $admin->permissions()->count();
        echo "✅ Added " . count($addedPermissionIds) . " new permissions to admin user\n";
        echo "✅ Admin user now has {$totalAdminPerms} total permissions\n";
    } else {
        echo "❌ Admin user not found\n";
    }
}

echo "\n🎉 Pranota Rit & Pranota Rit Kenek Permissions Setup Complete!\n";

// Display final permission list
echo "\n📝 All Pranota Rit Permissions:\n";
foreach ($pranotaRitPermissions as $perm) {
    echo "   • {$perm['name']}: {$perm['description']}\n";
}

echo "\n📝 All Pranota Rit Kenek Permissions:\n";
foreach ($pranotaRitKenekPermissions as $perm) {
    echo "   • {$perm['name']}: {$perm['description']}\n";
}

?>