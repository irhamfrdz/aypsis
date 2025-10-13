<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== REAL-TIME DEBUG UNTUK SIDEBAR TUJUAN KIRIM ===\n\n";

$userAdmin = User::where('username', 'user_admin')->first();

if (!$userAdmin) {
    echo "❌ User admin tidak ditemukan!\n";
    exit(1);
}

echo "✅ User ditemukan: {$userAdmin->username}\n";
echo "✅ Status: {$userAdmin->status}\n\n";

// Test semua kondisi yang mempengaruhi sidebar
echo "=== KONDISI SIDEBAR ===\n";

$user = $userAdmin;
$hasKaryawan = $user && $user->karyawan;
$isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');

echo "1. Has Karyawan: " . ($hasKaryawan ? '✅ YES' : '❌ NO') . "\n";
echo "2. Is Admin (role): " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n";

// Test semua permission yang mempengaruhi master section
$masterPermissions = [
    'master-permission-view',
    'master-cabang-view',
    'master-pengirim-view',
    'master-jenis-barang-view',
    'master-term-view',
    'master-coa-view',
    'master-kode-nomor-view',
    'master-nomor-terakhir-view',
    'master-tipe-akun-view',
    'master-tujuan-view',
    'master-tujuan-kirim-view',
    'master-kegiatan-view'
];

echo "\n3. MASTER PERMISSIONS CHECK:\n";
$hasMasterPermissions = false;
foreach ($masterPermissions as $perm) {
    $has = $user->can($perm);
    echo "   - {$perm}: " . ($has ? '✅ YES' : '❌ NO') . "\n";
    if ($has) $hasMasterPermissions = true;
}

echo "\n4. Has Any Master Permission: " . ($hasMasterPermissions ? '✅ YES' : '❌ NO') . "\n";

$showSidebar = $hasKaryawan || $isAdmin || $user;
echo "5. Show Sidebar: " . ($showSidebar ? '✅ YES' : '❌ NO') . "\n";

$showMasterSection = $isAdmin || $hasMasterPermissions;
echo "6. Show Master Section: " . ($showMasterSection ? '✅ YES' : '❌ NO') . "\n";

// Test spesifik untuk tujuan kirim
echo "\n=== TUJUAN KIRIM SPESIFIK ===\n";
$canViewTujuanKirim = $user && $user->can('master-tujuan-kirim-view');
echo "7. Can View Tujuan Kirim: " . ($canViewTujuanKirim ? '✅ YES' : '❌ NO') . "\n";

// Test route
try {
    $route = route('tujuan-kirim.index');
    echo "8. Route exists: ✅ YES - {$route}\n";
} catch (Exception $e) {
    echo "8. Route exists: ❌ NO - {$e->getMessage()}\n";
}

echo "\n=== DIAGNOSIS ===\n";
if ($showMasterSection && $canViewTujuanKirim) {
    echo "🎉 SEMUA KONDISI TERPENUHI! Menu seharusnya muncul.\n";
    echo "\n💡 KEMUNGKINAN PENYEBAB:\n";
    echo "1. Cache browser - Lakukan hard refresh (Ctrl+F5)\n";
    echo "2. Session cache - Logout dan login kembali\n";
    echo "3. JavaScript error - Check browser console\n";
    echo "4. Dropdown collapsed - Klik 'Master Data' untuk expand\n";
    echo "5. Permission middleware belum ter-sync\n";
} else {
    echo "❌ ADA KONDISI YANG TIDAK TERPENUHI!\n";
    if (!$showMasterSection) {
        echo "   - Master section tidak akan muncul\n";
    }
    if (!$canViewTujuanKirim) {
        echo "   - User tidak punya permission master-tujuan-kirim-view\n";
    }
}

echo "\n=== QUICK FIX ===\n";
echo "Jalankan command berikut:\n";
echo "1. php artisan cache:clear\n";
echo "2. php artisan view:clear\n";
echo "3. php artisan config:clear\n";
echo "4. Refresh browser dengan Ctrl+F5\n";
echo "5. Logout dan login kembali\n\n";

// Cek permission di database
$permission = \App\Models\Permission::where('name', 'master-tujuan-kirim-view')->first();
if ($permission) {
    $userHasPermission = $user->permissions()->where('permission_id', $permission->id)->exists();
    echo "=== DATABASE CHECK ===\n";
    echo "Permission exists in DB: ✅ YES (ID: {$permission->id})\n";
    echo "User has permission in pivot: " . ($userHasPermission ? '✅ YES' : '❌ NO') . "\n";
    
    if (!$userHasPermission) {
        echo "\n🔧 FIXING PERMISSION...\n";
        $user->permissions()->syncWithoutDetaching([$permission->id]);
        echo "✅ Permission assigned to user_admin\n";
    }
} else {
    echo "❌ Permission 'master-tujuan-kirim-view' tidak ada di database!\n";
}