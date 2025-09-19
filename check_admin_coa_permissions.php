<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔍 Memeriksa Permission COA User Admin\n";
echo "=====================================\n\n";

$user = User::where('username', 'admin')->first();

if ($user) {
    echo "✅ User admin ditemukan:\n";
    echo "   - Name: {$user->name}\n";
    echo "   - Username: {$user->username}\n\n";

    echo "🔐 Permission COA Check:\n";
    echo "   - master-coa: " . ($user->can('master-coa') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-coa-view: " . ($user->can('master-coa-view') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-coa-create: " . ($user->can('master-coa-create') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-coa-update: " . ($user->can('master-coa-update') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-coa-delete: " . ($user->can('master-coa-delete') ? '✅ YES' : '❌ NO') . "\n\n";

    // Cek apakah user adalah admin berdasarkan role
    echo "👤 Role Check:\n";
    echo "   - Is Admin: " . ($user->hasRole('admin') ? '✅ YES' : '❌ NO') . "\n\n";

    // Cek semua permissions yang dimiliki user
    echo "📋 Semua Permissions User:\n";
    $allPermissions = $user->getAllPermissions();
    if ($allPermissions->count() > 0) {
        foreach ($allPermissions as $perm) {
            if (strpos($perm->name, 'coa') !== false) {
                echo "   - {$perm->name}: {$perm->description}\n";
            }
        }
    } else {
        echo "   ❌ Tidak ada permissions yang ditemukan\n";
    }

} else {
    echo "❌ User admin tidak ditemukan di database\n";
    echo "💡 Mungkin user admin belum dibuat atau menggunakan nama yang berbeda\n\n";

    echo "👥 Daftar semua user yang ada:\n";
    $allUsers = User::all();
    foreach ($allUsers as $u) {
        echo "   - {$u->username} ({$u->name})\n";
    }
}

echo "\n🔧 Saran Perbaikan:\n";
echo "==================\n";
echo "1. Pastikan user admin memiliki permission 'master-coa-view'\n";
echo "2. Atau pastikan user admin memiliki role 'admin' yang bypass permission\n";
echo "3. Cek apakah middleware permission berfungsi dengan benar\n";
echo "4. Pastikan route middleware sudah terdaftar dengan benar\n";
