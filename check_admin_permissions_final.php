<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CEK PERMISSION USER ADMIN DENGAN SISTEM YANG BENAR ===\n\n";

try {
    // Get permissions untuk user admin
    echo "=== PERMISSION YANG DIMILIKI USER ADMIN ===\n";
    $permissions = DB::select("
        SELECT p.id, p.name, p.description
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = 1
        ORDER BY p.name
    ");

    echo "Total permissions: " . count($permissions) . "\n\n";

    $targetPermissions = ['cabang', 'coa', 'kode_nomor', 'nomor_terakhir', 'tipe_akun', 'tujuan'];
    $adminPermissions = array_column($permissions, 'name');

    echo "=== CEK PERMISSION YANG BERMASALAH ===\n";
    foreach ($targetPermissions as $target) {
        $viewPerm = $target . '.view';
        $indexPerm = $target . '.index';

        $hasView = in_array($viewPerm, $adminPermissions);
        $hasIndex = in_array($indexPerm, $adminPermissions);

        echo sprintf("%-15s: view=%s, index=%s\n",
            $target,
            $hasView ? '✅' : '❌',
            $hasIndex ? '✅' : '❌'
        );

        if (!$hasView && !$hasIndex) {
            echo "  ⚠️  Tidak ada permission view atau index untuk $target\n";
        }
    }

    echo "\n=== SEMUA PERMISSION USER ADMIN ===\n";
    foreach ($permissions as $perm) {
        echo "- {$perm->name}\n";
    }

    echo "\n=== CEK APAKAH ADA CACHE PERMISSION ===\n";
    // Cek apakah ada cache yang mungkin menyimpan permission lama
    $cacheFiles = glob(storage_path('framework/cache/data/*'));
    echo "Cache files: " . count($cacheFiles) . "\n";

    // Cek session files
    $sessionFiles = glob(storage_path('framework/sessions/*'));
    echo "Session files: " . count($sessionFiles) . "\n";

    echo "\n=== SIMULASI TEST PERMISSION ===\n";
    // Load User model dan test permission
    $userModel = new App\Models\User();
    $user = $userModel->find(1);

    if ($user) {
        echo "✅ User model loaded\n";

        foreach ($targetPermissions as $target) {
            $viewPerm = $target . '.view';
            $indexPerm = $target . '.index';

            // Test menggunakan method can() dari User model
            $canView = $user->can($viewPerm);
            $canIndex = $user->can($indexPerm);

            echo sprintf("%-15s: can(%s)=%s, can(%s)=%s\n",
                $target,
                $viewPerm, $canView ? '✅' : '❌',
                $indexPerm, $canIndex ? '✅' : '❌'
            );
        }
    } else {
        echo "❌ User model tidak bisa di-load\n";
    }

    echo "\n=== REKOMENDASI TINDAK LANJUT ===\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Clear config: php artisan config:clear\n";
    echo "3. Clear route: php artisan route:clear\n";
    echo "4. Clear session: rm storage/framework/sessions/*\n";
    echo "5. Logout dan login ulang\n";
    echo "6. Test dengan browser/session baru (incognito)\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
