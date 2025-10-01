<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CEK DETAIL VALIDASI USER ADMIN ===\n\n";

try {
    // Get user admin dengan semua field
    $user = DB::table('users')->where('username', 'admin')->first();

    if (!$user) {
        echo "❌ User admin tidak ditemukan\n";
        exit;
    }

    echo "✅ User admin ditemukan\n";
    echo "=== DETAIL USER ===\n";
    foreach ((array) $user as $field => $value) {
        echo sprintf("%-20s: %s\n", $field, $value ?? 'NULL');
    }

    echo "\n=== CEK KARYAWAN TERKAIT ===\n";
    $karyawan = DB::table('karyawans')->where('id', $user->karyawan_id)->first();
    if ($karyawan) {
        echo "✅ Karyawan ditemukan:\n";
        foreach ((array) $karyawan as $field => $value) {
            echo sprintf("%-20s: %s\n", $field, $value ?? 'NULL');
        }
    } else {
        echo "❌ Karyawan tidak ditemukan untuk ID: " . $user->karyawan_id . "\n";
    }

    echo "\n=== CEK PERMISSION LANGSUNG DI DATABASE ===\n";
    $permissions = DB::select("
        SELECT mp.name, up.can_view, up.can_create, up.can_update, up.can_delete
        FROM user_permissions up
        JOIN master_permissions mp ON up.master_permission_id = mp.id
        WHERE up.user_id = ?
        AND mp.name IN ('cabang', 'coa', 'kode_nomor', 'nomor_terakhir', 'tipe_akun', 'tujuan')
        ORDER BY mp.name
    ", [$user->id]);

    foreach ($permissions as $perm) {
        echo sprintf("%-15s: view=%s, create=%s, update=%s, delete=%s\n",
            $perm->name, $perm->can_view, $perm->can_create, $perm->can_update, $perm->can_delete);
    }

    echo "\n=== CEK AUTH GUARD DAN SESSION ===\n";

    // Cek apakah ada custom guard atau provider
    $authConfig = config('auth');
    echo "Default Guard: " . $authConfig['defaults']['guard'] . "\n";
    echo "Default Provider: " . $authConfig['defaults']['passwords'] . "\n";

    // Cek guard web
    if (isset($authConfig['guards']['web'])) {
        echo "Web Guard Driver: " . $authConfig['guards']['web']['driver'] . "\n";
        echo "Web Guard Provider: " . $authConfig['guards']['web']['provider'] . "\n";
    }

    // Cek provider users
    if (isset($authConfig['providers']['users'])) {
        echo "Users Provider Driver: " . $authConfig['providers']['users']['driver'] . "\n";
        echo "Users Provider Model: " . $authConfig['providers']['users']['model'] . "\n";
    }

    echo "\n=== CEK ROUTES YANG BERMASALAH ===\n";

    // Load routes dan cek apakah ada middleware tambahan
    $app = app();
    $router = $app['router'];

    $problematicRoutes = [
        'cabang.index',
        'coa.index',
        'kode_nomor.index',
        'nomor_terakhir.index',
        'tipe_akun.index',
        'tujuan.index'
    ];

    foreach ($problematicRoutes as $routeName) {
        try {
            $route = $router->getRoutes()->getByName($routeName);
            if ($route) {
                echo "Route: $routeName\n";
                echo "  URI: " . $route->uri() . "\n";
                echo "  Middleware: " . implode(', ', $route->middleware()) . "\n";
                echo "  Action: " . $route->getActionName() . "\n\n";
            } else {
                echo "❌ Route $routeName tidak ditemukan\n";
            }
        } catch (Exception $e) {
            echo "❌ Error mengecek route $routeName: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== REKOMENDASI DEBUGGING ===\n";
    echo "1. Cek Laravel Log: storage/logs/laravel.log\n";
    echo "2. Enable Query Log di controller\n";
    echo "3. Cek Browser Console untuk JavaScript errors\n";
    echo "4. Cek Network tab di Developer Tools\n";
    echo "5. Test dengan user lain yang memiliki permission sama\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
