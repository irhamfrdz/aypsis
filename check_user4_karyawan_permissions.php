<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 Cek Permission User4 untuk Master Karyawan\n";
echo "=============================================\n\n";

// Cek user4
$user4 = User::where('username', 'user4')->first();
if (!$user4) {
    echo "❌ User 'user4' tidak ditemukan\n";
    exit;
}

echo "👤 User: user4 (ID: {$user4->id})\n";
echo "   Email: {$user4->email}\n";
echo "   Status: " . ($user4->active ? 'Active' : 'Inactive') . "\n\n";

// Cek semua permissions user4
$permissions = $user4->permissions->pluck('name')->sort();
echo "📋 Semua Permissions User4:\n";
foreach ($permissions as $perm) {
    echo "   - {$perm}\n";
}
echo "\n";

// Filter permission karyawan
$karyawanPermissions = $permissions->filter(function($perm) {
    return str_starts_with($perm, 'master-karyawan');
});

echo "🏢 Permission Master Karyawan:\n";
if ($karyawanPermissions->count() > 0) {
    foreach ($karyawanPermissions as $perm) {
        echo "   ✅ {$perm}\n";
    }
} else {
    echo "   ❌ Tidak ada permission master-karyawan\n";
}
echo "\n";

// Test akses permission
$permissionsToTest = [
    'master-karyawan' => 'Menu utama karyawan',
    'master-karyawan.view' => 'View karyawan',
    'master-karyawan.create' => 'Create karyawan',
    'master-karyawan.update' => 'Update karyawan',
    'master-karyawan.delete' => 'Delete karyawan',
    'master-karyawan.print' => 'Print karyawan',
    'master-karyawan.export' => 'Export karyawan',
];

echo "🧪 Test Permission Access:\n";
foreach ($permissionsToTest as $perm => $desc) {
    $hasAccess = $user4->hasPermissionTo($perm);
    echo "   {$desc} ({$perm}): " . ($hasAccess ? '✅' : '❌') . "\n";
}
echo "\n";

// Cek apakah ada permission lama (format dot)
$oldKaryawanPermissions = $permissions->filter(function($perm) {
    return str_starts_with($perm, 'master.karyawan');
});

if ($oldKaryawanPermissions->count() > 0) {
    echo "⚠️  PERINGATAN: Masih ada permission lama (format dot):\n";
    foreach ($oldKaryawanPermissions as $perm) {
        echo "   - {$perm}\n";
    }
    echo "\n";
}

// Cek permission dashboard (untuk akses umum)
$hasDashboard = $user4->hasPermissionTo('dashboard');
echo "📊 Dashboard Access: " . ($hasDashboard ? '✅' : '❌') . "\n\n";

echo "🎯 Kesimpulan:\n";
if ($karyawanPermissions->count() > 0) {
    echo "   ✅ User4 memiliki permission master-karyawan\n";
    echo "   💡 Jika menu tidak muncul di sidebar, periksa:\n";
    echo "      1. Logic sidebar di blade template\n";
    echo "      2. Route middleware\n";
    echo "      3. Cache aplikasi\n";
} else {
    echo "   ❌ User4 TIDAK memiliki permission master-karyawan\n";
    echo "   💡 Periksa form permission dan pastikan checkbox dicentang\n";
}

echo "\n🔍 Pengecekan selesai!\n";
