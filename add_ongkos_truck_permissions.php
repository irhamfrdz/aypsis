<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\Role;

try {
    // Daftar permission untuk Ongkos Truck
    $permissions = [
        'ongkos-truck-list',
        'ongkos-truck-view',
        'ongkos-truck-create',
        'ongkos-truck-update',
        'ongkos-truck-delete',
        'ongkos-truck-export',
    ];

    echo "=== Menambahkan Permission Ongkos Truck ===\n\n";

    foreach ($permissions as $permission) {
        $existingPermission = Permission::where('name', $permission)->first();
        
        if (!$existingPermission) {
            Permission::create(['name' => $permission]);
            echo "✓ Permission '$permission' berhasil ditambahkan\n";
        } else {
            echo "○ Permission '$permission' sudah ada\n";
        }
    }

    echo "\n=== Memberikan Permission ke Role Admin ===\n\n";

    // Berikan semua permission ke role admin
    $adminRole = Role::where('name', 'admin')->first();
    
    if ($adminRole) {
        foreach ($permissions as $permission) {
            $permissionModel = Permission::where('name', $permission)->first();
            
            if ($permissionModel) {
                // Langsung insert tanpa cek, biarkan database handle unique constraint
                try {
                    \DB::table('permission_role')->insert([
                        'role_id' => $adminRole->id,
                        'permission_id' => $permissionModel->id,
                    ]);
                    echo "✓ Permission '$permission' diberikan ke role Admin\n";
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        echo "○ Role Admin sudah memiliki permission '$permission'\n";
                    } else {
                        throw $e;
                    }
                }
            }
        }
    } else {
        echo "✗ Role Admin tidak ditemukan\n";
    }

    echo "\n=== Selesai ===\n";
    echo "Total permission ditambahkan: " . count($permissions) . "\n";
    echo "\nDaftar Permission Ongkos Truck:\n";
    echo "- ongkos-truck-list   : Akses halaman daftar ongkos truck\n";
    echo "- ongkos-truck-view   : Melihat detail ongkos truck\n";
    echo "- ongkos-truck-create : Membuat ongkos truck baru\n";
    echo "- ongkos-truck-update : Mengubah data ongkos truck\n";
    echo "- ongkos-truck-delete : Menghapus data ongkos truck\n";
    echo "- ongkos-truck-export : Export data ongkos truck ke Excel\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
