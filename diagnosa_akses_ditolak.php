<?php

// Script untuk diagnosa menyeluruh masalah akses ditolak

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "=== DIAGNOSA MENYELURUH MASALAH AKSES DITOLAK ===\n\n";

// 1. Periksa user admin
$admin = User::where('username', 'admin')->with('permissions')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan!\n";
    exit;
}

echo "✅ User admin ditemukan (ID: {$admin->id})\n";
echo "Username: {$admin->username}\n";
echo "Total permissions: " . $admin->permissions->count() . "\n\n";

// 2. Periksa permission view yang diperlukan untuk menu bermasalah
$requiredViewPermissions = [
    'master-cabang-view',
    'master-coa-view',
    'master-kode-nomor-view',
    'master-nomor-terakhir-view',
    'master-tipe-akun-view',
    'master-tujuan-view'
];

echo "=== CEK PERMISSION VIEW YANG DIPERLUKAN ===\n";
foreach ($requiredViewPermissions as $permission) {
    $hasPermission = $admin->can($permission);
    $status = $hasPermission ? '✅ ADA' : '❌ TIDAK ADA';
    echo "- {$permission}: {$status}\n";

    // Cek apakah permission ada di database
    $permExists = $admin->permissions->contains('name', $permission);
    if (!$permExists && !$hasPermission) {
        echo "  ⚠️  Permission tidak ada dalam database user\n";
    }
}
echo "\n";

// 3. Periksa routes yang bermasalah
echo "=== CEK ROUTES DAN MIDDLEWARE ===\n";
$problematicRoutes = [
    'master.cabang.index' => 'master-cabang-view',
    'master-coa-index' => 'master-coa-view',
    'master.kode-nomor.index' => 'master-kode-nomor-view',
    'master.nomor-terakhir.index' => 'master-nomor-terakhir-view',
    'master.tipe-akun.index' => 'master-tipe-akun-view',
    'master.tujuan.index' => 'master-tujuan-view'
];

foreach ($problematicRoutes as $routeName => $requiredPermission) {
    echo "🔍 Route: {$routeName}\n";

    try {
        $route = Route::getRoutes()->getByName($routeName);
        if ($route) {
            echo "  ✅ Route exists\n";
            echo "  📍 URL: " . $route->uri() . "\n";
            echo "  🔒 Required permission: {$requiredPermission}\n";

            // Cek middleware
            $middleware = $route->middleware();
            echo "  🛡️  Middleware: " . implode(', ', $middleware) . "\n";

            // Test permission
            $canAccess = $admin->can($requiredPermission);
            echo "  🎯 User can access: " . ($canAccess ? '✅ YES' : '❌ NO') . "\n";
        } else {
            echo "  ❌ Route tidak ditemukan!\n";
        }
    } catch (Exception $e) {
        echo "  ⚠️  Error checking route: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// 4. Periksa apakah ada permission dengan nama yang mirip
echo "=== CEK PERMISSION DENGAN NAMA MIRIP ===\n";
$allPermissions = $admin->permissions->pluck('name')->toArray();
foreach ($requiredViewPermissions as $required) {
    $similar = array_filter($allPermissions, function($perm) use ($required) {
        $requiredParts = explode('-', $required);
        $module = $requiredParts[0] . '-' . $requiredParts[1]; // master-cabang
        return str_contains($perm, $module);
    });

    if (!empty($similar)) {
        echo "Module " . explode('-view', $required)[0] . ":\n";
        foreach ($similar as $perm) {
            echo "  - {$perm}\n";
        }
        echo "\n";
    }
}

// 5. Test method can() langsung
echo "=== TEST METHOD can() LANGSUNG ===\n";
foreach ($requiredViewPermissions as $permission) {
    // Test hasPermissionTo
    $hasPermissionTo = $admin->hasPermissionTo($permission);
    // Test can() method
    $can = $admin->can($permission);
    // Test hasFlexiblePermission (method baru kita)

    echo "Permission: {$permission}\n";
    echo "  hasPermissionTo(): " . ($hasPermissionTo ? '✅' : '❌') . "\n";
    echo "  can(): " . ($can ? '✅' : '❌') . "\n";
    echo "\n";
}

// 6. Cek apakah ada masalah dengan session atau cache
echo "=== INFORMASI TAMBAHAN ===\n";
echo "User ID: {$admin->id}\n";
echo "Created at: {$admin->created_at}\n";
echo "Updated at: {$admin->updated_at}\n";

// 7. Cek apakah user memiliki karyawan terkait
if ($admin->karyawan) {
    echo "Karyawan terkait: {$admin->karyawan->nama_lengkap}\n";
} else {
    echo "Tidak ada karyawan terkait\n";
}

echo "\n=== REKOMENDASI TROUBLESHOOTING ===\n";
echo "1. Clear browser cache dan cookies\n";
echo "2. Logout dan login kembali\n";
echo "3. Periksa apakah ada middleware tambahan\n";
echo "4. Periksa log aplikasi Laravel\n";
echo "5. Test dengan user lain selain admin\n";
