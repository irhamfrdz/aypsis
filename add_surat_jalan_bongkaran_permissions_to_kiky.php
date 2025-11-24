<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;

try {
    // Cari user kiky
    $user = User::where('username', 'kiky')->first();
    
    if (!$user) {
        echo "User 'kiky' tidak ditemukan!\n";
        exit(1);
    }
    
    echo "User ditemukan: {$user->name} ({$user->username})\n\n";
    
    // Daftar permissions untuk surat jalan bongkaran
    $permissions = [
        'surat-jalan-bongkaran-list',
        'surat-jalan-bongkaran-view',
        'surat-jalan-bongkaran-create',
        'surat-jalan-bongkaran-update',
        'surat-jalan-bongkaran-delete'
    ];
    
    $addedPermissions = [];
    $existingPermissions = [];
    
    foreach ($permissions as $permissionName) {
        // Cek apakah permission sudah ada di database
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            // Buat permission baru jika belum ada
            $permission = Permission::create(['name' => $permissionName]);
            echo "Permission '{$permissionName}' berhasil dibuat\n";
        }
        
        // Cek apakah user sudah memiliki permission ini
        if (!$user->hasPermissionTo($permissionName)) {
            $user->permissions()->attach($permission);
            $addedPermissions[] = $permissionName;
            echo "Permission '{$permissionName}' berhasil diberikan ke user kiky\n";
        } else {
            $existingPermissions[] = $permissionName;
            echo "Permission '{$permissionName}' sudah dimiliki oleh user kiky\n";
        }
    }
    
    echo "\n=== RINGKASAN ===\n";
    echo "User: {$user->name} ({$user->username})\n";
    echo "Permissions yang ditambahkan: " . count($addedPermissions) . "\n";
    echo "Permissions yang sudah ada: " . count($existingPermissions) . "\n";
    
    if (count($addedPermissions) > 0) {
        echo "\nPermissions baru yang ditambahkan:\n";
        foreach ($addedPermissions as $perm) {
            echo "- {$perm}\n";
        }
    }
    
    if (count($existingPermissions) > 0) {
        echo "\nPermissions yang sudah ada sebelumnya:\n";
        foreach ($existingPermissions as $perm) {
            echo "- {$perm}\n";
        }
    }
    
    echo "\nSemua permissions surat jalan bongkaran berhasil diberikan ke user kiky!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}