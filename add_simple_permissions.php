<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "🔧 Menambahkan Permission Sederhana untuk User test2\n";
echo "=================================================\n\n";

// Find user test2
$user = User::where('username', 'test2')->first();

if (!$user) {
    echo "❌ User test2 not found\n";
    exit(1);
}

echo "✅ Found user: {$user->username} (ID: {$user->id})\n\n";

// Permission yang akan ditambahkan
$permissionsToAdd = [
    'tagihan-kontainer' => 'Akses menu Tagihan Kontainer Sewa',
    'pranota-supir' => 'Akses menu Pranota Supir',
    'pembayaran-pranota-supir' => 'Akses menu Pembayaran Pranota Supir',
];

echo "📋 Permission yang akan ditambahkan:\n";
foreach ($permissionsToAdd as $permName => $description) {
    echo "  - {$permName}: {$description}\n";
}
echo "\n";

// Cek permission yang sudah ada
$userPermissions = $user->permissions->pluck('name')->toArray();
echo "📋 Permission yang sudah dimiliki:\n";
foreach ($userPermissions as $perm) {
    echo "  - {$perm}\n";
}
echo "\n";

// Tambahkan permission yang belum ada
$addedPermissions = [];
$skippedPermissions = [];

foreach ($permissionsToAdd as $permName => $description) {
    if (in_array($permName, $userPermissions)) {
        echo "⏭️  Permission '{$permName}' sudah ada, dilewati\n";
        $skippedPermissions[] = $permName;
        continue;
    }

    // Cari atau buat permission
    $permission = Permission::firstOrCreate(
        ['name' => $permName],
        ['description' => $description]
    );

    // Attach ke user
    if (!$user->permissions->contains($permission->id)) {
        $user->permissions()->attach($permission->id);
        echo "✅ Permission '{$permName}' berhasil ditambahkan\n";
        $addedPermissions[] = $permName;
    } else {
        echo "⏭️  Permission '{$permName}' sudah terattach, dilewati\n";
        $skippedPermissions[] = $permName;
    }
}

echo "\n";

// Verifikasi hasil
$user->refresh();
$newPermissions = $user->permissions->pluck('name')->toArray();

echo "🎯 Hasil Akhir - Permission User test2:\n";
echo "======================================\n";
foreach ($newPermissions as $perm) {
    $isNew = in_array($perm, $addedPermissions);
    $marker = $isNew ? '🆕' : '📋';
    echo "  {$marker} {$perm}\n";
}

echo "\n📊 Ringkasan:\n";
echo "=============\n";
echo "  ✅ Permission ditambahkan: " . count($addedPermissions) . "\n";
echo "  ⏭️  Permission dilewati: " . count($skippedPermissions) . "\n";
echo "  📋 Total permission sekarang: " . count($newPermissions) . "\n";

if (!empty($addedPermissions)) {
    echo "\n🎉 Permission baru berhasil ditambahkan!\n";
    echo "   User test2 sekarang bisa mengakses menu:\n";
    foreach ($addedPermissions as $perm) {
        $menuName = match($perm) {
            'tagihan-kontainer' => 'Tagihan Kontainer Sewa',
            'pranota-supir' => 'Pranota Supir',
            'pembayaran-pranota-supir' => 'Pembayaran Pranota Supir',
            default => $perm
        };
        echo "   - ✅ {$menuName}\n";
    }
} else {
    echo "\nℹ️  Tidak ada permission baru yang ditambahkan\n";
}

echo "\n🚀 Sistem permission sederhana siap digunakan!\n";
