<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Adding Tagihan CAT permissions...\n";

$permissions = [
    [
        'name' => 'tagihan-cat.view',
        'description' => 'Melihat daftar tagihan CAT',
    ],
    [
        'name' => 'tagihan-cat.create',
        'description' => 'Membuat tagihan CAT baru',
    ],
    [
        'name' => 'tagihan-cat.update',
        'description' => 'Mengupdate data tagihan CAT',
    ],
    [
        'name' => 'tagihan-cat.delete',
        'description' => 'Menghapus data tagihan CAT',
    ],
];

$added = 0;
$skipped = 0;

foreach ($permissions as $permission) {
    $exists = DB::table('permissions')->where('name', $permission['name'])->exists();

    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $permission['name'],
            'description' => $permission['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Added permission: {$permission['name']}\n";
        $added++;
    } else {
        echo "- Skipped existing permission: {$permission['name']}\n";
        $skipped++;
    }
}

echo "\nSummary:\n";
echo "Added: $added permissions\n";
echo "Skipped: $skipped permissions\n";
echo "Total: " . ($added + $skipped) . " permissions processed\n";

echo "\nAssigning permissions to admin user...\n";

// Find admin user
$adminUser = \App\Models\User::where('username', 'admin')->first();

if ($adminUser) {
    echo "Found admin user: {$adminUser->username} (ID: {$adminUser->id})\n";

    // Get the permission IDs for tagihan-cat permissions
    $permissionIds = [];
    foreach ($permissions as $permission) {
        $permissionRecord = DB::table('permissions')->where('name', $permission['name'])->first();
        if ($permissionRecord) {
            $permissionIds[] = $permissionRecord->id;
        }
    }

    if (!empty($permissionIds)) {
        // Assign permissions to admin user
        $adminUser->permissions()->syncWithoutDetaching($permissionIds);
        echo "✓ Assigned " . count($permissionIds) . " tagihan-cat permissions to admin user\n";

        // Refresh user to get updated permissions
        $adminUser->refresh();
        $newPermissions = $adminUser->permissions->whereIn('name', array_column($permissions, 'name'))->pluck('name')->toArray();

        echo "✅ Admin user now has " . count($newPermissions) . " tagihan-cat permissions:\n";
        foreach ($newPermissions as $perm) {
            echo "  - {$perm}\n";
        }
    } else {
        echo "❌ No permission IDs found to assign\n";
    }
} else {
    echo "Admin user not found!\n";
}

echo "\nTagihan CAT permissions setup completed!\n";
