<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TAMBAHKAN PERMISSION INDEX YANG HILANG ===\n\n";

try {
    $userId = 1; // admin user

    // Permission index yang hilang berdasarkan test sebelumnya
    $missingIndexPermissions = [
        'master-cabang-index',
        'master-kode-nomor-index',
        'master-nomor-terakhir-index',
        'master-tipe-akun-index'
    ];

    echo "=== CEK DAN BUAT PERMISSION YANG HILANG ===\n";

    foreach ($missingIndexPermissions as $permissionName) {
        // Cek apakah permission exists di tabel permissions
        $permission = DB::table('permissions')
            ->where('name', $permissionName)
            ->first();

        if (!$permission) {
            // Buat permission baru
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => $permissionName,
                'description' => ucwords(str_replace(['-', '_'], ' ', $permissionName)),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo "➕ DIBUAT permission: $permissionName (ID: $permissionId)\n";

            // Tambahkan ke user admin
            DB::table('user_permissions')->insert([
                'user_id' => $userId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo "   ✅ Ditambahkan ke user admin\n";

        } else {
            echo "✅ Permission '$permissionName' sudah ada (ID: {$permission->id})\n";

            // Cek apakah user sudah punya permission ini
            $userHasPermission = DB::table('user_permissions')
                ->where('user_id', $userId)
                ->where('permission_id', $permission->id)
                ->exists();

            if (!$userHasPermission) {
                DB::table('user_permissions')->insert([
                    'user_id' => $userId,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "   ➕ Ditambahkan ke user admin\n";
            } else {
                echo "   ✅ User admin sudah punya permission ini\n";
            }
        }
    }

    echo "\n=== VERIFIKASI ULANG PERMISSION ===\n";

    // Load user dan test ulang
    $user = \App\Models\User::find(1);

    $allTestPermissions = [
        'master-cabang-view',
        'master-cabang-index',
        'master-cabang-create',
        'master-coa-view',
        'master-coa-index',
        'master-coa-create',
        'master-kode-nomor-view',
        'master-kode-nomor-index',
        'master-kode-nomor-create',
        'master-nomor-terakhir-view',
        'master-nomor-terakhir-index',
        'master-nomor-terakhir-create',
        'master-tipe-akun-view',
        'master-tipe-akun-index',
        'master-tipe-akun-create',
        'master-tujuan-view',
        'master-tujuan-index',
        'master-tujuan-create'
    ];

    $allPass = true;

    foreach ($allTestPermissions as $perm) {
        $canAccess = $user->can($perm);
        $status = $canAccess ? '✅' : '❌';
        echo "$status $perm\n";

        if (!$canAccess) {
            $allPass = false;
        }
    }

    echo "\n=== HASIL FINAL ===\n";
    if ($allPass) {
        echo "🎉 SEMUA PERMISSION SUDAH LENGKAP!\n";
        echo "✅ User admin sekarang memiliki akses penuh ke semua modul master\n";

        // Clear cache sekali lagi
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            echo "✅ Cache cleared\n";
        } catch (Exception $e) {
            echo "⚠️  Cache clear gagal: " . $e->getMessage() . "\n";
        }

        echo "\n=== INSTRUKSI FINAL ===\n";
        echo "1. 🔄 RESTART server Laravel (php artisan serve)\n";
        echo "2. 🚪 LOGOUT dari aplikasi\n";
        echo "3. 🔄 Tutup browser sepenuhnya\n";
        echo "4. 🚪 LOGIN kembali\n";
        echo "5. ✅ Test semua menu master: Cabang, COA, Kode Nomor, dll\n";

    } else {
        echo "⚠️  Masih ada permission yang belum benar\n";
        echo "💡 Mungkin perlu troubleshooting lebih lanjut\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
