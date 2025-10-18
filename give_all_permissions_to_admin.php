<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== MEMBERIKAN SEMUA PERMISSION KE USER ADMIN ===\n\n";

// Cari user admin
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan!\n";
    exit(1);
}

echo "👤 User: {$admin->username}\n";
echo "📧 Email: {$admin->email}\n\n";

// Ambil semua permission yang ada
$allPermissions = Permission::all();
echo "📋 Total permission di sistem: " . $allPermissions->count() . "\n\n";

// Ambil permission yang sudah dimiliki admin
$currentPermissions = $admin->permissions()->pluck('permission_id')->toArray();
echo "🔐 Permission yang sudah dimiliki: " . count($currentPermissions) . "\n";

// Hitung permission yang belum dimiliki
$allPermissionIds = $allPermissions->pluck('id')->toArray();
$missingPermissions = array_diff($allPermissionIds, $currentPermissions);

echo "➕ Permission yang akan ditambahkan: " . count($missingPermissions) . "\n\n";

if (count($missingPermissions) > 0) {
    echo "🔄 Menambahkan permission yang belum ada...\n";

    // Tambahkan permission yang belum ada (tanpa menghapus yang sudah ada)
    $admin->permissions()->syncWithoutDetaching($missingPermissions);

    echo "✅ Berhasil menambahkan " . count($missingPermissions) . " permission baru\n\n";
} else {
    echo "✅ User admin sudah memiliki semua permission\n\n";
}

// Verifikasi akhir
$finalPermissions = $admin->permissions()->count();
$totalPermissions = Permission::count();

echo "=" . str_repeat("=", 48) . "\n";
echo "📊 HASIL AKHIR:\n";
echo "   Total permission sistem: {$totalPermissions}\n";
echo "   Permission admin: {$finalPermissions}\n";

if ($finalPermissions == $totalPermissions) {
    echo "   Status: ✅ ADMIN MEMILIKI SEMUA PERMISSION\n";
} else {
    echo "   Status: ❌ Ada " . ($totalPermissions - $finalPermissions) . " permission yang belum dimiliki\n";
}

echo "=" . str_repeat("=", 48) . "\n";

// Tampilkan beberapa permission penting untuk verifikasi
echo "\n🔍 Verifikasi permission penting:\n";

$importantPermissions = [
    'dashboard',
    'audit-log-view',
    'audit-log-export',
    'master-karyawan-view',
    'master-karyawan-create',
    'master-karyawan-update',
    'master-karyawan-delete',
    'order-management-view',
    'surat-jalan-view',
    'approval-dashboard'
];

foreach ($importantPermissions as $permName) {
    $hasPermission = $admin->can($permName);
    $status = $hasPermission ? '✅' : '❌';
    echo "   {$status} {$permName}\n";
}

echo "\n🎉 Proses selesai! User admin sekarang memiliki akses penuh ke semua fitur sistem.\n";
