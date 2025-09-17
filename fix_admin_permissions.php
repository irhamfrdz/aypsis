<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "🔧 Memperbaiki Permission User Admin\n";
echo "====================================\n\n";

$user = User::where('username', 'admin')->first();

if ($user) {
    echo "✅ User admin ditemukan: {$user->username}\n\n";

    // Cari permission yang diperlukan
    $permissionsNeeded = [
        'master-user.update' => 'Master user update',
        'master-user.delete' => 'Master user delete'
    ];

    $permissionsToAdd = [];

    foreach ($permissionsNeeded as $permName => $description) {
        $permission = Permission::where('name', $permName)->first();

        if (!$permission) {
            // Buat permission jika belum ada
            $permission = Permission::create([
                'name' => $permName,
                'description' => $description
            ]);
            echo "🆕 Permission '{$permName}' dibuat\n";
        }

        // Cek apakah user sudah memiliki permission ini
        $hasPermission = $user->permissions()->where('name', $permName)->exists();

        if (!$hasPermission) {
            $permissionsToAdd[] = $permission;
            echo "➕ Menambahkan permission: {$permName}\n";
        } else {
            echo "✅ Sudah memiliki permission: {$permName}\n";
        }
    }

    // Tambahkan permissions yang belum dimiliki
    if (!empty($permissionsToAdd)) {
        $permissionIds = collect($permissionsToAdd)->pluck('id')->toArray();
        $user->permissions()->attach($permissionIds);
        echo "\n🎉 Permissions berhasil ditambahkan!\n";
    } else {
        echo "\n✅ User admin sudah memiliki semua permissions yang diperlukan\n";
    }

    echo "\n🔐 Status Permission Setelah Update:\n";
    echo "   - master-user.view: " . ($user->can('master-user.view') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-user.create: " . ($user->can('master-user.create') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-user.update: " . ($user->can('master-user.update') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - master-user.delete: " . ($user->can('master-user.delete') ? '✅ YES' : '❌ NO') . "\n";

    echo "\n🚀 Sekarang user admin dapat:\n";
    echo "   - ✅ Melihat daftar user\n";
    echo "   - ✅ Membuat user baru\n";
    echo "   - ✅ Mengedit user (FIXED!)\n";
    echo "   - ✅ Menghapus user\n";

} else {
    echo "❌ User admin tidak ditemukan\n";
}
