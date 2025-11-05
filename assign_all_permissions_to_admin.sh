#!/bin/bash
# Script untuk memberikan semua permission kepada user admin

echo "🔐 MEMBERIKAN SEMUA PERMISSION UNTUK USER ADMIN..."
echo "================================================="

# Jalankan PHP script untuk assign semua permission
php -r "
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Permission;

try {
    // Cari user admin
    \$admin = User::where('username', 'admin')->first();
    
    if (!\$admin) {
        echo '❌ User admin tidak ditemukan!' . PHP_EOL;
        exit(1);
    }
    
    echo '👤 User admin ditemukan: ' . \$admin->name . ' (' . \$admin->username . ')' . PHP_EOL;
    
    // Get semua permission yang ada
    \$allPermissions = Permission::all();
    echo '📋 Total permissions tersedia: ' . \$allPermissions->count() . PHP_EOL;
    
    // Hapus permission lama admin terlebih dahulu
    \$admin->permissions()->detach();
    
    // Assign semua permission ke admin
    \$permissionIds = \$allPermissions->pluck('id')->toArray();
    \$admin->permissions()->attach(\$permissionIds);
    
    echo '✅ Semua permission berhasil diberikan ke user admin!' . PHP_EOL;
    echo '📊 Permission yang diberikan:' . PHP_EOL;
    
    foreach (\$allPermissions as \$permission) {
        echo '   - ' . \$permission->name . PHP_EOL;
    }
    
    echo PHP_EOL . '🎉 SELESAI! User admin sekarang memiliki akses ke semua fitur.' . PHP_EOL;
    
} catch (Exception \$e) {
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}
"