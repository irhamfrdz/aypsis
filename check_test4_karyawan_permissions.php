<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 Cek Permission Test4 untuk Master Karyawan\n";
echo "=============================================\n\n";

// Cek test4
$userTest4 = User::where('username', 'test4')->first();
if (!$userTest4) {
    echo "❌ User 'test4' tidak ditemukan\n";
    exit;
}

echo "👤 User: test4 (ID: {$userTest4->id})\n";
echo "   Email: {$userTest4->email}\n";
echo "   Status: " . ($userTest4->active ? 'Active' : 'Inactive') . "\n\n";

// Cek semua permissions test4
$permissions = $userTest4->permissions->pluck('name')->sort();
echo "📋 Semua Permissions Test4:\n";
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
    $hasAccess = $userTest4->hasPermissionTo($perm);
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
$hasDashboard = $userTest4->hasPermissionTo('dashboard');
echo "📊 Dashboard Access: " . ($hasDashboard ? '✅' : '❌') . "\n\n";

echo "🎯 Kesimpulan:\n";
if ($karyawanPermissions->count() > 0) {
    echo "   ✅ Test4 memiliki permission master-karyawan\n";
    echo "   💡 Jika menu tidak muncul di sidebar, periksa:\n";
    echo "      1. Logic sidebar di blade template\n";
    echo "      2. Route middleware\n";
    echo "      3. Cache aplikasi\n";
} else {
    echo "   ❌ Test4 TIDAK memiliki permission master-karyawan\n";
    echo "   💡 Periksa form permission dan pastikan checkbox dicentang\n";
}

echo "\n🔍 Pengecekan selesai!\n";
