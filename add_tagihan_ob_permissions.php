<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Adding Tagihan OB permissions...\n";

$permissions = [
    [
        'name' => 'tagihan-ob-view',
        'description' => 'Melihat daftar tagihan OB',
    ],
    [
        'name' => 'tagihan-ob-create',
        'description' => 'Membuat tagihan OB baru',
    ],
    [
        'name' => 'tagihan-ob-update',
        'description' => 'Mengupdate data tagihan OB',
    ],
    [
        'name' => 'tagihan-ob-delete',
        'description' => 'Menghapus data tagihan OB',
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

echo "\nAdding permissions to admin user...\n";

// Get admin user (usually ID 1)
$adminUser = DB::table('users')->where('id', 1)->first();

if ($adminUser) {
    echo "Found admin user ID: {$adminUser->id}\n";
    
    foreach ($permissions as $permission) {
        $permissionRecord = DB::table('permissions')->where('name', $permission['name'])->first();
        
        if ($permissionRecord) {
            $exists = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->where('permission_id', $permissionRecord->id)
                ->exists();
                
            if (!$exists) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permissionRecord->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✓ Added permission {$permission['name']} to admin\n";
            } else {
                echo "- Admin already has permission: {$permission['name']}\n";
            }
        }
    }
} else {
    echo "Admin user not found. Please assign permissions manually.\n";
}

echo "\nSummary:\n";
echo "Added: {$added} permissions\n";
echo "Skipped: {$skipped} permissions\n";
echo "Done!\n";