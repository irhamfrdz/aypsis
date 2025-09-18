<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔍 Memeriksa Permission User Admin\n";
echo "==================================\n\n";

$user = User::where('username', 'admin')->first();

if ($user) {
    echo "✅ User admin ditemukan:\n";
    echo "   - Name: {$user->name}\n";
    echo "   - Email: {$user->email}\n";
    echo "   - Permissions: " . ($user->permissions ?: 'Tidak ada permissions') . "\n\n";

    echo "🔐 Permission Check:\n";
    echo "   - master-user.view: " . ($user->can('master-user.view') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-user.create: " . ($user->can('master-user.create') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-user.update: " . ($user->can('master-user.update') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-user.delete: " . ($user->can('master-user.delete') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pranota-perbaikan-kontainer.view: " . ($user->can('pranota-perbaikan-kontainer.view') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pranota-perbaikan-kontainer.delete: " . ($user->can('pranota-perbaikan-kontainer.delete') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pranota-perbaikan-kontainer-view: " . ($user->can('pranota-perbaikan-kontainer-view') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pranota-perbaikan-kontainer-delete: " . ($user->can('pranota-perbaikan-kontainer-delete') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-user: " . ($user->can('master-user') ? '✅ YES' : '❌ NO') . "\n\n";

    // Cek apakah user adalah admin berdasarkan role
    echo "👤 Role Check:\n";
    echo "   - Is Admin: " . ($user->hasRole('admin') ? '✅ YES' : '❌ NO') . "\n";

    // Cek semua permissions yang dimiliki user
    echo "📋 Semua Permissions User:\n";
    $allPermissions = $user->getAllPermissions();
    if ($allPermissions->count() > 0) {
        foreach ($allPermissions as $perm) {
            echo "   - {$perm->name}: {$perm->description}\n";
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
        echo "   - {$u->name} ({$u->email})\n";
    }
}

echo "\n🔧 Saran Perbaikan:\n";
echo "==================\n";
echo "1. Pastikan user admin memiliki permission 'master-user.update'\n";
echo "2. Atau pastikan user admin memiliki role 'admin' yang bypass permission\n";
echo "3. Cek apakah middleware permission berfungsi dengan benar\n";
echo "4. Pastikan route middleware sudah terdaftar dengan benar\n";
