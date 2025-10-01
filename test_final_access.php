<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFIKASI FINAL PERMISSION USER ADMIN ===\n\n";

try {
    // Load User model dan test permission
    $userModel = new App\Models\User();
    $user = $userModel->find(1);

    if (!$user) {
        echo "❌ User admin tidak ditemukan\n";
        exit;
    }

    echo "✅ User admin loaded: {$user->username}\n\n";

    // Test permission untuk routes yang bermasalah
    $testRoutes = [
        'cabang' => ['master-cabang-view', 'master-cabang-create', 'master-cabang-index'],
        'coa' => ['master-coa-view', 'master-coa-create', 'master-coa-index'],
        'kode_nomor' => ['master-kode-nomor-view', 'master-kode-nomor-create', 'master-kode-nomor-index'],
        'nomor_terakhir' => ['master-nomor-terakhir-view', 'master-nomor-terakhir-create', 'master-nomor-terakhir-index'],
        'tipe_akun' => ['master-tipe-akun-view', 'master-tipe-akun-create', 'master-tipe-akun-index'],
        'tujuan' => ['master-tujuan-view', 'master-tujuan-create', 'master-tujuan-index']
    ];

    echo "=== TEST PERMISSION DENGAN USER MODEL ===\n";

    foreach ($testRoutes as $module => $permissions) {
        echo "\n📋 MODULE: " . strtoupper($module) . "\n";

        foreach ($permissions as $permission) {
            $hasPermission = $user->can($permission);
            $status = $hasPermission ? '✅' : '❌';
            echo "  $status can('$permission')\n";
        }
    }

    echo "\n=== CEK PERMISSION DI DATABASE ===\n";

    // Cek permission langsung di database
    $userPermissions = DB::select("
        SELECT p.name
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ?
        AND (
            p.name LIKE 'master-cabang-%' OR
            p.name LIKE 'master-coa-%' OR
            p.name LIKE 'master-kode-nomor-%' OR
            p.name LIKE 'master-nomor-terakhir-%' OR
            p.name LIKE 'master-tipe-akun-%' OR
            p.name LIKE 'master-tujuan-%'
        )
        ORDER BY p.name
    ", [1]);

    $permissionNames = array_column($userPermissions, 'name');

    foreach ($testRoutes as $module => $permissions) {
        echo "\n📋 $module:\n";
        foreach ($permissions as $permission) {
            $exists = in_array($permission, $permissionNames);
            $status = $exists ? '✅' : '❌';
            echo "  $status $permission\n";
        }
    }

    echo "\n=== SIMULASI AKSES ROUTE ===\n";

    // Test route access simulation
    $routeTests = [
        'master.cabang.index' => 'master-cabang-view',
        'master-coa-index' => 'master-coa-view',
        'master.kode_nomor.index' => 'master-kode-nomor-view',
        'master.nomor_terakhir.index' => 'master-nomor-terakhir-view',
        'master.tipe_akun.index' => 'master-tipe-akun-view',
        'master.tujuan.index' => 'master-tujuan-view'
    ];

    foreach ($routeTests as $routeName => $requiredPermission) {
        $hasAccess = $user->can($requiredPermission);
        $status = $hasAccess ? '🟢 AKSES GRANTED' : '🔴 AKSES DENIED';
        echo "$status - Route: $routeName (need: $requiredPermission)\n";
    }

    echo "\n=== INSTRUKSI UNTUK USER ===\n";
    echo "1. 🚪 LOGOUT dari aplikasi\n";
    echo "2. 🔄 Tutup browser/clear cookies\n";
    echo "3. 🚪 LOGIN kembali dengan username: admin\n";
    echo "4. 🧪 Test akses menu: Cabang, COA, Kode Nomor, etc.\n";
    echo "5. ✅ Seharusnya tidak ada lagi 'Akses Ditolak'\n";

    echo "\n=== TROUBLESHOOTING TAMBAHAN ===\n";
    echo "Jika masih bermasalah, coba:\n";
    echo "- Buka Developer Tools (F12)\n";
    echo "- Check Console untuk JavaScript errors\n";
    echo "- Check Network tab untuk HTTP 403 errors\n";
    echo "- Pastikan tidak ada custom middleware yang block\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
