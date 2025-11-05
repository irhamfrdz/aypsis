<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Permission;

echo "🔐 MEMBERIKAN SEMUA PERMISSION UNTUK USER ADMIN...\n";
echo "==================================================\n";

try {
    // Cari user admin
    $admin = User::where('username', 'admin')->first();
    
    if (!$admin) {
        echo "❌ User admin tidak ditemukan!\n";
        exit(1);
    }
    
    echo "👤 User admin ditemukan: {$admin->name} ({$admin->username})\n";
    
    // Get semua permission yang ada
    $allPermissions = Permission::all();
    echo "📋 Total permissions tersedia: {$allPermissions->count()}\n";
    
    // Hapus permission lama admin terlebih dahulu
    $admin->permissions()->detach();
    
    // Assign semua permission ke admin
    $permissionIds = $allPermissions->pluck('id')->toArray();
    $admin->permissions()->attach($permissionIds);
    
    echo "✅ Semua permission berhasil diberikan ke user admin!\n";
    echo "📊 Permission yang diberikan:\n";
    
    foreach ($allPermissions as $permission) {
        echo "   - {$permission->name}\n";
    }
    
    echo "\n🎉 SELESAI! User admin sekarang memiliki akses ke semua fitur.\n";
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    exit(1);
}