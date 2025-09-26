<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "🔧 Memberikan Permission Pembayaran Pranota CAT ke User Admin\n";
echo "==========================================================\n\n";

$user = User::where('username', 'admin')->first();

if ($user) {
    echo "✅ User admin ditemukan: {$user->username}\n\n";

    // Permission yang diperlukan untuk pembayaran pranota cat
    $permissionsNeeded = [
        'pembayaran-pranota-cat-view',
        'pembayaran-pranota-cat-create',
        'pembayaran-pranota-cat-update',
        'pembayaran-pranota-cat-delete',
        'pembayaran-pranota-cat-print',
        'pembayaran-pranota-cat-export'
    ];

    $permissionsToAdd = [];

    foreach ($permissionsNeeded as $permName) {
        $permission = Permission::where('name', $permName)->first();

        if (!$permission) {
            echo "❌ Permission '{$permName}' tidak ditemukan di database\n";
            continue;
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
        echo "\n✅ User admin sudah memiliki semua permissions pembayaran pranota cat\n";
    }

    echo "\n🔐 Status Permission Pembayaran Pranota CAT:\n";
    echo "   - pembayaran-pranota-cat-view: " . ($user->can('pembayaran-pranota-cat-view') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pembayaran-pranota-cat-create: " . ($user->can('pembayaran-pranota-cat-create') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pembayaran-pranota-cat-update: " . ($user->can('pembayaran-pranota-cat-update') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pembayaran-pranota-cat-delete: " . ($user->can('pembayaran-pranota-cat-delete') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pembayaran-pranota-cat-print: " . ($user->can('pembayaran-pranota-cat-print') ? '✅ YES' : '❌ NO') . "\n";
    echo "   - pembayaran-pranota-cat-export: " . ($user->can('pembayaran-pranota-cat-export') ? '✅ YES' : '❌ NO') . "\n";

    // Cek apakah user memiliki role admin
    $hasAdminRole = method_exists($user, 'hasRole') && $user->hasRole('admin');
    echo "\n👤 Role Admin: " . ($hasAdminRole ? '✅ YES' : '❌ NO') . "\n";

    echo "\n📊 Total permissions user admin: " . $user->permissions()->count() . "\n";

} else {
    echo "❌ User admin tidak ditemukan\n";
}

echo "\n🎯 Menu 'Bayar Pranota CAT Kontainer' akan muncul jika:\n";
echo "   - User memiliki role 'admin', ATAU\n";
echo "   - User memiliki permission 'pembayaran-pranota-cat-view'\n\n";
