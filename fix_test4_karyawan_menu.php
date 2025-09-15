<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔧 Fix Permission Test4 untuk Menu Master Karyawan\n";
echo "=================================================\n\n";

$userTest4 = User::where('username', 'test4')->first();
if (!$userTest4) {
    echo "❌ User 'test4' tidak ditemukan\n";
    exit;
}

echo "👤 User: test4 (ID: {$userTest4->id})\n";
echo "   Status saat ini: {$userTest4->status}\n\n";

// 1. Pastikan user approved jika belum
if ($userTest4->status !== 'approved') {
    $userTest4->status = 'approved';
    $userTest4->save();
    echo "✅ User test4 status diubah ke 'approved'\n\n";
} else {
    echo "✅ User test4 sudah approved\n\n";
}

// 2. Cek permission master-karyawan utama
$hasMainPermission = $userTest4->hasPermissionTo('master-karyawan');
echo "📋 Permission master-karyawan utama: " . ($hasMainPermission ? '✅ ADA' : '❌ TIDAK ADA') . "\n";

// 3. Jika tidak ada permission utama, tambahkan
if (!$hasMainPermission) {
    $mainPermissionId = DB::table('permissions')->where('name', 'master-karyawan')->value('id');
    if ($mainPermissionId) {
        try {
            DB::table('user_permissions')->insert([
                'user_id' => $userTest4->id,
                'permission_id' => $mainPermissionId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Permission 'master-karyawan' berhasil ditambahkan\n";
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                echo "ℹ️  Permission 'master-karyawan' sudah ada\n";
            } else {
                throw $e;
            }
        }
    } else {
        echo "❌ Permission 'master-karyawan' tidak ditemukan di database\n";
    }
} else {
    echo "✅ Permission 'master-karyawan' sudah ada\n";
}

echo "\n";

// 4. Refresh dan verifikasi semua permissions
$userTest4->refresh();
$allPermissions = $userTest4->permissions->pluck('name')->sort();

echo "📋 Semua Permissions Test4 setelah update:\n";
foreach ($allPermissions as $perm) {
    echo "   - {$perm}\n";
}
echo "\n";

// 5. Test akses menu
$menuPermissions = [
    'master-karyawan' => 'Menu utama karyawan',
    'master-karyawan.view' => 'View karyawan',
    'master-karyawan.create' => 'Create karyawan',
    'master-karyawan.update' => 'Update karyawan',
    'master-karyawan.delete' => 'Delete karyawan',
];

echo "🧪 Test Akses Menu:\n";
$menuAccessible = false;
foreach ($menuPermissions as $perm => $desc) {
    $hasAccess = $userTest4->hasPermissionTo($perm);
    echo "   {$desc} ({$perm}): " . ($hasAccess ? '✅' : '❌') . "\n";
    if ($perm === 'master-karyawan' && $hasAccess) {
        $menuAccessible = true;
    }
}

echo "\n🎯 Kesimpulan:\n";
if ($menuAccessible) {
    echo "   ✅ Menu Master Karyawan seharusnya muncul di sidebar\n";
    echo "   💡 Pastikan untuk:\n";
    echo "      1. Logout dan login kembali\n";
    echo "      2. Clear cache browser (Ctrl+F5)\n";
    echo "      3. Cek apakah ada cache aplikasi Laravel\n";
} else {
    echo "   ❌ Menu Master Karyawan masih tidak akan muncul\n";
    echo "   🔧 Perlu menambahkan permission 'master-karyawan'\n";
}

echo "\n🔧 Fix selesai!\n";
