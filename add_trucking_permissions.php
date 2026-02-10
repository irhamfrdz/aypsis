<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "-------- MEMULAI PENAMBAHAN PERMISSIONS --------\n";

$permissionsData = [
    'master-pricelist-biaya-trucking-view',
    'master-pricelist-biaya-trucking-create',
    'master-pricelist-biaya-trucking-update',
    'master-pricelist-biaya-trucking-delete',
];

$permissionIds = [];

foreach ($permissionsData as $name) {
    // Cek apakah permission sudah ada
    $permission = Permission::where('name', $name)->first();
    
    if (!$permission) {
        try {
            $permission = Permission::create([
                'name' => $name,
                'description' => 'Permission for ' . $name
            ]);
            echo "[SUKSES] Permission '$name' berhasil dibuat.\n";
        } catch (\Exception $e) {
            echo "[GAGAL] Gagal membuat '$name': " . $e->getMessage() . "\n";
            continue;
        }
    } else {
        echo "[ADA] Permission '$name' sudah ada.\n";
    }

    if ($permission) {
        $permissionIds[] = $permission->id;
    }
}

echo "\n-------- MENUGASKAN KE ROLE ADMIN --------\n";

$role = Role::where('name', 'admin')->first();

if ($role) {
    try {
        // Attach permissions to role (ignore duplicates)
        $role->permissions()->syncWithoutDetaching($permissionIds);
        echo "[SUKSES] Semua permission berhasil ditambahkan ke role 'admin'.\n";
    } catch (\Exception $e) {
        echo "[GAGAL] Gagal menugaskan permission ke admin: " . $e->getMessage() . "\n";
    }
} else {
    echo "[WARNING] Role 'admin' tidak ditemukan.\n";
}

echo "\n-------- SELESAI --------\n";
