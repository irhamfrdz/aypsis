<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TAMBAHKAN PERMISSION YANG HILANG UNTUK USER ADMIN ===\n\n";

try {
    $userId = 1; // admin user

    // Permission yang dibutuhkan untuk akses penuh ke menu master
    $neededPermissions = [
        'master-cabang-index',
        'master-cabang-create',
        'master-coa-index',
        'master-coa-create',
        'master-kode-nomor-index',
        'master-kode-nomor-create',
        'master-nomor-terakhir-index',
        'master-nomor-terakhir-create',
        'master-tipe-akun-index',
        'master-tipe-akun-create',
        'master-tujuan-index',
        'master-tujuan-create'
    ];

    echo "=== CEK DAN TAMBAHKAN PERMISSION ===\n";

    foreach ($neededPermissions as $permissionName) {
        // Cek apakah permission exists di tabel permissions
        $permission = DB::table('permissions')
            ->where('name', $permissionName)
            ->first();

        if (!$permission) {
            echo "❌ Permission '$permissionName' tidak ada di tabel permissions\n";
            continue;
        }

        // Cek apakah user sudah punya permission ini
        $userHasPermission = DB::table('user_permissions')
            ->where('user_id', $userId)
            ->where('permission_id', $permission->id)
            ->exists();

        if ($userHasPermission) {
            echo "✅ User sudah punya: $permissionName\n";
        } else {
            // Tambahkan permission ke user
            DB::table('user_permissions')->insert([
                'user_id' => $userId,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "➕ DITAMBAHKAN: $permissionName\n";
        }
    }

    echo "\n=== CEK HASIL SETELAH PENAMBAHAN ===\n";

    // Re-check permission user admin
    $userPermissions = DB::select("
        SELECT p.name
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ?
        AND p.name LIKE 'master-cabang-%'
           OR p.name LIKE 'master-coa-%'
           OR p.name LIKE 'master-kode-nomor-%'
           OR p.name LIKE 'master-nomor-terakhir-%'
           OR p.name LIKE 'master-tipe-akun-%'
           OR p.name LIKE 'master-tujuan-%'
        ORDER BY p.name
    ", [$userId]);

    echo "Permission terkait master yang dimiliki user admin:\n";
    foreach ($userPermissions as $perm) {
        echo "- {$perm->name}\n";
    }

    echo "\n=== CLEAR CACHE LARAVEL ===\n";
    echo "Menjalankan clear cache...\n";

    // Clear various caches
    $commands = [
        'cache:clear',
        'config:clear',
        'route:clear',
        'view:clear'
    ];

    foreach ($commands as $command) {
        try {
            \Illuminate\Support\Facades\Artisan::call($command);
            echo "✅ $command berhasil\n";
        } catch (Exception $e) {
            echo "❌ $command gagal: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== SOLUSI FINAL ===\n";
    echo "1. ✅ Permission yang dibutuhkan sudah ditambahkan\n";
    echo "2. ✅ Cache Laravel sudah dibersihkan\n";
    echo "3. 🔄 SILAKAN LOGOUT DAN LOGIN KEMBALI\n";
    echo "4. 🔄 Atau refresh halaman dengan Ctrl+F5\n";
    echo "5. 🔄 Atau buka di tab/browser baru\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
