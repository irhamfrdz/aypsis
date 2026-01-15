<?php

/**
 * Script untuk menambahkan permissions Stock Ban
 * 
 * Cara menjalankan:
 * php add_stock_ban_permissions.php
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "=== Menambahkan Permissions Stock Ban ===\n\n";

    // Daftar permissions yang akan ditambahkan
    $permissions = [
        ['name' => 'stock-ban-view', 'description' => 'Melihat daftar dan detail stock ban'],
        ['name' => 'stock-ban-create', 'description' => 'Menambah stock ban baru'],
        ['name' => 'stock-ban-update', 'description' => 'Mengubah data stock ban'],
        ['name' => 'stock-ban-delete', 'description' => 'Menghapus stock ban'],
    ];

    $createdPermissions = [];
    $existingPermissions = [];

    foreach ($permissions as $permData) {
        // Cek apakah permission sudah ada
        $permission = App\Models\Permission::where('name', $permData['name'])->first();
        
        if (!$permission) {
            // Buat permission baru
            $permission = App\Models\Permission::create([
                'name' => $permData['name'],
                'description' => $permData['description'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $createdPermissions[] = $permData['name'];
            echo "✓ Permission '{$permData['name']}' berhasil dibuat\n";
        } else {
            $existingPermissions[] = $permData['name'];
            echo "○ Permission '{$permData['name']}' sudah ada\n";
        }
    }

    echo "\n=== Menambahkan Permissions ke User Admin ===\n\n";

    // Tambahkan semua permissions ke user admin
    $admin = App\Models\User::where('username', 'admin')->first();
    
    if ($admin) {
        foreach ($permissions as $permData) {
            $permission = App\Models\Permission::where('name', $permData['name'])->first();
            if ($permission && !$admin->permissions->contains($permission->id)) {
                $admin->permissions()->attach($permission->id);
                echo "✓ Permission '{$permData['name']}' ditambahkan ke user admin\n";
            } else {
                echo "○ User admin sudah memiliki permission '{$permData['name']}'\n";
            }
        }
    } else {
        echo "⚠ User 'admin' tidak ditemukan!\n";
    }

    echo "\n=== Summary ===\n";
    echo "Permissions baru dibuat: " . count($createdPermissions) . "\n";
    echo "Permissions sudah ada: " . count($existingPermissions) . "\n";
    
    if (count($createdPermissions) > 0) {
        echo "\nPermissions yang baru dibuat:\n";
        foreach ($createdPermissions as $perm) {
            echo "  - {$perm}\n";
        }
    }

    echo "\n✓ Proses selesai!\n";

} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
