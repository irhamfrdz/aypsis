<?php
/**
 * Script untuk menambahkan permission master transportasi yang sama seperti tujuan kirim
 * Menambahkan: master-transportasi-view, master-transportasi-create, master-transportasi-update, master-transportasi-delete
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel aplikasi
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Menambahkan permission master transportasi...\n\n";

try {
    // Permission yang akan ditambahkan (sama seperti tujuan kirim)
    $permissions = [
        [
            'name' => 'master-transportasi-view',
            'description' => 'Melihat Data Master Transportasi'
        ],
        [
            'name' => 'master-transportasi-create', 
            'description' => 'Membuat Data Master Transportasi'
        ],
        [
            'name' => 'master-transportasi-update',
            'description' => 'Mengupdate Data Master Transportasi'
        ],
        [
            'name' => 'master-transportasi-delete',
            'description' => 'Menghapus Data Master Transportasi'
        ]
    ];

    $now = now();
    $createdCount = 0;
    $existingCount = 0;

    foreach ($permissions as $permissionData) {
        // Cek apakah permission sudah ada
        $existingPermission = DB::table('permissions')
            ->where('name', $permissionData['name'])
            ->first();

        if (!$existingPermission) {
            // Insert permission baru
            DB::table('permissions')->insert([
                'name' => $permissionData['name'],
                'description' => $permissionData['description'],
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now
            ]);
            
            echo "✅ Permission '{$permissionData['name']}' berhasil ditambahkan\n";
            $createdCount++;
        } else {
            echo "ℹ️  Permission '{$permissionData['name']}' sudah ada (ID: {$existingPermission->id})\n";
            $existingCount++;
        }
    }

    echo "\n📊 Ringkasan:\n";
    echo "   - Permission baru: {$createdCount}\n";
    echo "   - Permission sudah ada: {$existingCount}\n";

    // Assign permission ke role admin
    echo "\n🔑 Assign permission ke role admin...\n";
    
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    if ($adminRole) {
        $assignedCount = 0;
        
        foreach ($permissions as $permissionData) {
            $permission = DB::table('permissions')
                ->where('name', $permissionData['name'])
                ->first();
                
            if ($permission) {
                // Cek apakah role sudah memiliki permission
                $existingRoleHasPermission = DB::table('role_has_permissions')
                    ->where('role_id', $adminRole->id)
                    ->where('permission_id', $permission->id)
                    ->exists();
                    
                if (!$existingRoleHasPermission) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $adminRole->id,
                        'permission_id' => $permission->id
                    ]);
                    
                    echo "✅ Permission '{$permissionData['name']}' ditambahkan ke role admin\n";
                    $assignedCount++;
                } else {
                    echo "ℹ️  Permission '{$permissionData['name']}' sudah ada di role admin\n";
                }
            }
        }
        
        echo "\n📊 Ringkasan assignment:\n";
        echo "   - Permission baru di role admin: {$assignedCount}\n";
    } else {
        echo "⚠️  Role 'admin' tidak ditemukan!\n";
    }

    // Clear cache
    echo "\n🧹 Clearing cache...\n";
    if (function_exists('app')) {
        try {
            app()['cache']->forget('spatie.permission.cache');
            echo "✅ Permission cache cleared\n";
        } catch (Exception $e) {
            echo "⚠️  Error clearing cache: " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎉 Selesai! Permission master transportasi telah ditambahkan.\n";
    echo "\n📝 Permission yang ditambahkan:\n";
    echo "   - master-transportasi-view (untuk melihat data)\n";  
    echo "   - master-transportasi-create (untuk membuat data baru)\n";
    echo "   - master-transportasi-update (untuk mengupdate data)\n";
    echo "   - master-transportasi-delete (untuk menghapus data)\n";
    
    echo "\n✨ Struktur permission sekarang sama seperti tujuan kirim!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}