<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

// Permissions for Master Pengirim/Penerima
$permissions = [
    [
        'name' => 'master-pengirim-penerima-view',
        'description' => 'Melihat daftar master pengirim/penerima',
        'category' => 'Master Data'
    ],
    [
        'name' => 'master-pengirim-penerima-create',
        'description' => 'Membuat master pengirim/penerima baru',
        'category' => 'Master Data'
    ],
    [
        'name' => 'master-pengirim-penerima-update',
        'description' => 'Mengubah data master pengirim/penerima',
        'category' => 'Master Data'
    ],
    [
        'name' => 'master-pengirim-penerima-delete',
        'description' => 'Menghapus data master pengirim/penerima',
        'category' => 'Master Data'
    ],
];

echo "Adding Master Pengirim/Penerima permissions...\n";

foreach ($permissions as $permData) {
    $permission = Permission::firstOrCreate(
        ['name' => $permData['name']],
        [
            'description' => $permData['description'],
            'category' => $permData['category']
        ]
    );
    echo "✓ Permission created/found: {$permission->name}\n";
}

// Assign to admin users
echo "\nAssigning permissions to admin users...\n";
$adminUsers = User::where('role', 'admin')->get();

foreach ($adminUsers as $admin) {
    foreach ($permissions as $permData) {
        $permission = Permission::where('name', $permData['name'])->first();
        if ($permission && !$admin->permissions->contains($permission->id)) {
            $admin->permissions()->attach($permission->id);
            echo "✓ Assigned {$permission->name} to {$admin->name}\n";
        }
    }
}

echo "\n✅ All permissions added and assigned successfully!\n";
