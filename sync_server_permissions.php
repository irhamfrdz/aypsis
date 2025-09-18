<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== SERVER: Permission Synchronization ===\n";

echo "🔍 Step 1: Checking current server permissions...\n";
$currentPermissions = Permission::all();
$currentCount = $currentPermissions->count();
echo "Current permissions: $currentCount\n";

// Define the complete permission set that should exist
$requiredPermissions = [
    // Dashboard
    ['name' => 'dashboard', 'description' => 'Akses Dashboard Utama'],
    ['name' => 'dashboard.view', 'description' => 'Melihat Dashboard'],
    ['name' => 'dashboard.create', 'description' => 'Membuat Dashboard'],
    ['name' => 'dashboard.update', 'description' => 'Mengupdate Dashboard'],
    ['name' => 'dashboard.delete', 'description' => 'Menghapus Dashboard'],
    ['name' => 'dashboard.print', 'description' => 'Print Dashboard'],
    ['name' => 'dashboard.export', 'description' => 'Export Dashboard'],

    // Master Data - Core
    ['name' => 'master-karyawan', 'description' => 'Akses Master Karyawan'],
    ['name' => 'master-karyawan.view', 'description' => 'Melihat Master Karyawan'],
    ['name' => 'master-karyawan.create', 'description' => 'Membuat Master Karyawan'],
    ['name' => 'master-karyawan.update', 'description' => 'Mengupdate Master Karyawan'],
    ['name' => 'master-karyawan.delete', 'description' => 'Menghapus Master Karyawan'],
    ['name' => 'master-karyawan.print', 'description' => 'Print Master Karyawan'],
    ['name' => 'master-karyawan.export', 'description' => 'Export Master Karyawan'],
    ['name' => 'master.karyawan.index', 'description' => 'Index Master Karyawan'],
    ['name' => 'master.karyawan.create', 'description' => 'Create Master Karyawan'],
    ['name' => 'master.karyawan.store', 'description' => 'Store Master Karyawan'],
    ['name' => 'master.karyawan.show', 'description' => 'Show Master Karyawan'],
    ['name' => 'master.karyawan.edit', 'description' => 'Edit Master Karyawan'],
    ['name' => 'master.karyawan.update', 'description' => 'Update Master Karyawan'],
    ['name' => 'master.karyawan.destroy', 'description' => 'Destroy Master Karyawan'],

    ['name' => 'master-user', 'description' => 'Akses Master User'],
    ['name' => 'master-user.view', 'description' => 'Melihat Master User'],
    ['name' => 'master-user.create', 'description' => 'Membuat Master User'],
    ['name' => 'master-user.update', 'description' => 'Mengupdate Master User'],
    ['name' => 'master-user.delete', 'description' => 'Menghapus Master User'],
    ['name' => 'master-user.print', 'description' => 'Print Master User'],
    ['name' => 'master-user.export', 'description' => 'Export Master User'],

    ['name' => 'master-kontainer', 'description' => 'Akses Master Kontainer'],
    ['name' => 'master-kontainer.view', 'description' => 'Melihat Master Kontainer'],
    ['name' => 'master-kontainer.create', 'description' => 'Membuat Master Kontainer'],
    ['name' => 'master-kontainer.update', 'description' => 'Mengupdate Master Kontainer'],
    ['name' => 'master-kontainer.delete', 'description' => 'Menghapus Master Kontainer'],
    ['name' => 'master-kontainer.print', 'description' => 'Print Master Kontainer'],
    ['name' => 'master-kontainer.export', 'description' => 'Export Master Kontainer'],

    ['name' => 'master-tujuan', 'description' => 'Akses Master Tujuan'],
    ['name' => 'master-tujuan.view', 'description' => 'Melihat Master Tujuan'],
    ['name' => 'master-tujuan.create', 'description' => 'Membuat Master Tujuan'],
    ['name' => 'master-tujuan.update', 'description' => 'Mengupdate Master Tujuan'],
    ['name' => 'master-tujuan.delete', 'description' => 'Menghapus Master Tujuan'],
    ['name' => 'master-tujuan.print', 'description' => 'Print Master Tujuan'],
    ['name' => 'master-tujuan.export', 'description' => 'Export Master Tujuan'],

    ['name' => 'master-kegiatan', 'description' => 'Akses Master Kegiatan'],
    ['name' => 'master-kegiatan.view', 'description' => 'Melihat Master Kegiatan'],
    ['name' => 'master-kegiatan.create', 'description' => 'Membuat Master Kegiatan'],
    ['name' => 'master-kegiatan.update', 'description' => 'Mengupdate Master Kegiatan'],
    ['name' => 'master-kegiatan.delete', 'description' => 'Menghapus Master Kegiatan'],
    ['name' => 'master-kegiatan.print', 'description' => 'Print Master Kegiatan'],
    ['name' => 'master-kegiatan.export', 'description' => 'Export Master Kegiatan'],

    ['name' => 'master-permission', 'description' => 'Akses Master Permission'],
    ['name' => 'master-permission.view', 'description' => 'Melihat Master Permission'],
    ['name' => 'master-permission.create', 'description' => 'Membuat Master Permission'],
    ['name' => 'master-permission.update', 'description' => 'Mengupdate Master Permission'],
    ['name' => 'master-permission.delete', 'description' => 'Menghapus Master Permission'],
    ['name' => 'master-permission.print', 'description' => 'Print Master Permission'],
    ['name' => 'master-permission.export', 'description' => 'Export Master Permission'],

    ['name' => 'master-mobil', 'description' => 'Akses Master Mobil'],
    ['name' => 'master-mobil.view', 'description' => 'Melihat Master Mobil'],
    ['name' => 'master-mobil.create', 'description' => 'Membuat Master Mobil'],
    ['name' => 'master-mobil.update', 'description' => 'Mengupdate Master Mobil'],
    ['name' => 'master-mobil.delete', 'description' => 'Menghapus Master Mobil'],
    ['name' => 'master-mobil.print', 'description' => 'Print Master Mobil'],
    ['name' => 'master-mobil.export', 'description' => 'Export Master Mobil'],

    ['name' => 'master-divisi', 'description' => 'Akses Master Divisi'],
    ['name' => 'master-divisi.view', 'description' => 'Melihat Master Divisi'],
    ['name' => 'master-divisi.create', 'description' => 'Membuat Master Divisi'],
    ['name' => 'master-divisi.update', 'description' => 'Mengupdate Master Divisi'],
    ['name' => 'master-divisi.delete', 'description' => 'Menghapus Master Divisi'],
    ['name' => 'master-divisi.print', 'description' => 'Print Master Divisi'],
    ['name' => 'master-divisi.export', 'description' => 'Export Master Divisi'],

    ['name' => 'master-cabang', 'description' => 'Akses Master Cabang'],
    ['name' => 'master-cabang.view', 'description' => 'Melihat Master Cabang'],
    ['name' => 'master-cabang.create', 'description' => 'Membuat Master Cabang'],
    ['name' => 'master-cabang.update', 'description' => 'Mengupdate Master Cabang'],
    ['name' => 'master-cabang.delete', 'description' => 'Menghapus Master Cabang'],
    ['name' => 'master-cabang.print', 'description' => 'Print Master Cabang'],
    ['name' => 'master-cabang.export', 'description' => 'Export Master Cabang'],

    ['name' => 'master-pekerjaan', 'description' => 'Akses Master Pekerjaan'],
    ['name' => 'master-pekerjaan.view', 'description' => 'Melihat Master Pekerjaan'],
    ['name' => 'master-pekerjaan.create', 'description' => 'Membuat Master Pekerjaan'],
    ['name' => 'master-pekerjaan.update', 'description' => 'Mengupdate Master Pekerjaan'],
    ['name' => 'master-pekerjaan.delete', 'description' => 'Menghapus Master Pekerjaan'],
    ['name' => 'master-pekerjaan.print', 'description' => 'Print Master Pekerjaan'],
    ['name' => 'master-pekerjaan.export', 'description' => 'Export Master Pekerjaan'],

    ['name' => 'master-pajak', 'description' => 'Akses Master Pajak'],
    ['name' => 'master-pajak.view', 'description' => 'Melihat Master Pajak'],
    ['name' => 'master-pajak.create', 'description' => 'Membuat Master Pajak'],
    ['name' => 'master-pajak.update', 'description' => 'Mengupdate Master Pajak'],
    ['name' => 'master-pajak.delete', 'description' => 'Menghapus Master Pajak'],

    ['name' => 'master-bank', 'description' => 'Akses Master Bank'],
    ['name' => 'master-bank.view', 'description' => 'Melihat Master Bank'],
    ['name' => 'master-bank.create', 'description' => 'Membuat Master Bank'],
    ['name' => 'master-bank.update', 'description' => 'Mengupdate Master Bank'],
    ['name' => 'master-bank.delete', 'description' => 'Menghapus Master Bank'],

    ['name' => 'master-coa', 'description' => 'Akses Master COA'],
    ['name' => 'master-coa.view', 'description' => 'Melihat Master COA'],
    ['name' => 'master-coa.create', 'description' => 'Membuat Master COA'],
    ['name' => 'master-coa.update', 'description' => 'Mengupdate Master COA'],
    ['name' => 'master-coa.delete', 'description' => 'Menghapus Master COA'],

    ['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Akses Master Pricelist Sewa Kontainer'],
    ['name' => 'master-pricelist-sewa-kontainer.view', 'description' => 'Melihat Master Pricelist Sewa Kontainer'],
    ['name' => 'master-pricelist-sewa-kontainer.create', 'description' => 'Membuat Master Pricelist Sewa Kontainer'],
    ['name' => 'master-pricelist-sewa-kontainer.update', 'description' => 'Mengupdate Master Pricelist Sewa Kontainer'],
    ['name' => 'master-pricelist-sewa-kontainer.delete', 'description' => 'Menghapus Master Pricelist Sewa Kontainer'],
    ['name' => 'master-pricelist-sewa-kontainer.print', 'description' => 'Print Master Pricelist Sewa Kontainer'],
    ['name' => 'master-pricelist-sewa-kontainer.export', 'description' => 'Export Master Pricelist Sewa Kontainer'],

    // System permissions
    ['name' => 'login', 'description' => 'Login ke Sistem'],
    ['name' => 'logout', 'description' => 'Logout dari Sistem'],
    ['name' => 'profile', 'description' => 'Akses Profile'],
    ['name' => 'storage-local', 'description' => 'Akses Storage Lokal'],

    // Admin permissions
    ['name' => 'admin-debug-perms', 'description' => 'Debug Permissions'],
    ['name' => 'admin-features', 'description' => 'Admin Features'],
    ['name' => 'user-approval', 'description' => 'User Approval System'],
];

echo "\n🔧 Step 2: Synchronizing permissions...\n";

$added = 0;
$skipped = 0;

foreach ($requiredPermissions as $permData) {
    $existing = Permission::where('name', $permData['name'])->first();

    if (!$existing) {
        Permission::create($permData);
        echo "✅ Added: {$permData['name']}\n";
        $added++;
    } else {
        $skipped++;
    }
}

echo "\n📊 Step 3: Synchronization Summary:\n";
echo "Permissions added: $added\n";
echo "Permissions skipped (already exist): $skipped\n";

$newTotal = Permission::count();
echo "Total permissions after sync: $newTotal\n";

echo "\n🎯 Step 4: Verifying critical permissions:\n";
$criticalPerms = [
    'master-karyawan-view',
    'master-user-view',
    'master-kontainer-view',
    'master-pricelist-sewa-kontainer-view',
    'master-tujuan-view',
    'master-kegiatan-view',
    'master-permission-view',
    'master-mobil-view',
    'master-divisi-view',
    'master-cabang-view',
    'master-pekerjaan-view',
    'master-pajak-view',
    'master-bank-view',
    'master-coa-view'
];

$criticalFound = 0;
foreach ($criticalPerms as $perm) {
    $exists = Permission::where('name', $perm)->exists();
    echo "- $perm: " . ($exists ? '✅' : '❌') . "\n";
    if ($exists) $criticalFound++;
}

echo "\n📈 Final Summary:\n";
echo "Critical permissions found: $criticalFound/" . count($criticalPerms) . "\n";

if ($criticalFound == count($criticalPerms)) {
    echo "\n🎉 SUCCESS: All critical permissions synchronized!\n";
    echo "Master Data menu should now appear in sidebar.\n";
} else {
    echo "\n⚠️  WARNING: Some critical permissions still missing!\n";
    echo "Master Data menu may still not appear.\n";
}

echo "\n💡 Next Steps:\n";
echo "1. Clear cache: php artisan view:clear\n";
echo "2. Assign permissions to user_admin if needed\n";
echo "3. Test login and check sidebar\n";