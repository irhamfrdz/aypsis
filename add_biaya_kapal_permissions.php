<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\Role;

try {
    // Daftar permission untuk Biaya Kapal
    $permissions = [
        'biaya-kapal-view',
        'biaya-kapal-create',
        'biaya-kapal-update',
        'biaya-kapal-delete',
    ];

    echo "=== Menambahkan Permission Biaya Kapal ===\n\n";

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
        // Cek nama tabel pivot yang digunakan
        $tables = \DB::select("SHOW TABLES LIKE '%role%'");
        echo "Tables yang ditemukan: " . json_encode($tables) . "\n\n";
        
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

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
