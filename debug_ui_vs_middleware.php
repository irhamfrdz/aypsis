<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG PERMISSION UI vs DATABASE vs MIDDLEWARE ===\n\n";

try {
    $userId = 1; // admin user

    // Modules yang bermasalah
    $problematicModules = [
        'coa' => 'master-coa',
        'cabang' => 'master-cabang',
        'tipe_akun' => 'master-tipe-akun',
        'kode_nomor' => 'master-kode-nomor',
        'nomor_terakhir' => 'master-nomor-terakhir',
        'tujuan' => 'master-tujuan',
        'kegiatan' => 'master-kegiatan'
    ];

    echo "=== CEK PERMISSION DI DATABASE ===\n";

    foreach ($problematicModules as $module => $permissionPrefix) {
        echo "\n📋 MODULE: " . strtoupper($module) . "\n";

        // Cek permission yang ada di database untuk module ini
        $modulePermissions = DB::select("
            SELECT p.name
            FROM user_permissions up
            JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? AND p.name LIKE ?
            ORDER BY p.name
        ", [$userId, $permissionPrefix . '%']);

        if (empty($modulePermissions)) {
            echo "   ❌ TIDAK ADA PERMISSION untuk $permissionPrefix\n";
        } else {
            echo "   ✅ Permissions yang ditemukan:\n";
            foreach ($modulePermissions as $perm) {
                echo "      - {$perm->name}\n";
            }
        }

        // Test permission menggunakan User model
        $user = \App\Models\User::find($userId);

        $testPermissions = [
            $permissionPrefix . '-view',
            $permissionPrefix . '-index',
            $permissionPrefix . '-create'
        ];

        echo "   🧪 Test User->can():\n";
        foreach ($testPermissions as $testPerm) {
            $canAccess = $user->can($testPerm);
            $status = $canAccess ? '✅' : '❌';
            echo "      $status can('$testPerm')\n";
        }
    }

    echo "\n=== CEK ROUTE MIDDLEWARE CONFIGURATION ===\n";

    // Load routes dan cek middleware
    $router = app('router');
    $routesToCheck = [
        'master.cabang.index' => 'master-cabang-view',
        'master-coa-index' => 'master-coa-view',
        'master.tipe_akun.index' => 'master-tipe-akun-view',
        'master.kode_nomor.index' => 'master-kode-nomor-view',
        'master.nomor_terakhir.index' => 'master-nomor-terakhir-view',
        'master.tujuan.index' => 'master-tujuan-view',
        'master.kegiatan.index' => 'master-kegiatan-view'
    ];

    foreach ($routesToCheck as $routeName => $expectedPermission) {
        echo "\n🛣️  ROUTE: $routeName\n";

        try {
            $route = $router->getRoutes()->getByName($routeName);
            if ($route) {
                echo "   ✅ Route exists\n";
                echo "   📍 URI: " . $route->uri() . "\n";
                echo "   🛡️  Middleware: " . implode(', ', $route->middleware()) . "\n";

                // Cek apakah ada middleware 'can' dan permission apa yang dibutuhkan
                $middleware = $route->middleware();
                $canMiddleware = array_filter($middleware, function($m) {
                    return strpos($m, 'can:') === 0;
                });

                if (!empty($canMiddleware)) {
                    foreach ($canMiddleware as $canMW) {
                        $requiredPerm = str_replace('can:', '', $canMW);
                        echo "   🔑 Requires permission: $requiredPerm\n";

                        // Test apakah user punya permission ini
                        $user = \App\Models\User::find($userId);
                        $hasAccess = $user->can($requiredPerm);
                        $accessStatus = $hasAccess ? '✅ GRANTED' : '❌ DENIED';
                        echo "   🚪 Access: $accessStatus\n";

                        if (!$hasAccess) {
                            echo "   ⚠️  PROBLEM: User tidak punya permission '$requiredPerm'\n";
                        }
                    }
                } else {
                    echo "   ⚠️  NO 'can:' middleware found\n";
                }

            } else {
                echo "   ❌ Route NOT FOUND\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Error checking route: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== CEK CONTROLLER AUTHORIZATION ===\n";

    // Cek beberapa controller apakah ada authorize() calls
    $controllersToCheck = [
        'CabangController' => 'App\\Http\\Controllers\\CabangController',
        'MasterCoaController' => 'App\\Http\\Controllers\\MasterCoaController',
    ];

    foreach ($controllersToCheck as $name => $class) {
        echo "\n🎮 CONTROLLER: $name\n";

        if (class_exists($class)) {
            echo "   ✅ Controller exists\n";

            // Cek method index
            $reflection = new ReflectionClass($class);
            if ($reflection->hasMethod('index')) {
                echo "   ✅ Has index method\n";

                $method = $reflection->getMethod('index');
                $source = file_get_contents($method->getFileName());

                // Cek apakah ada authorize() call
                if (strpos($source, '$this->authorize(') !== false) {
                    echo "   🛡️  Uses authorize() method\n";
                } else {
                    echo "   ⚠️  NO authorize() method found\n";
                }

            } else {
                echo "   ❌ No index method\n";
            }
        } else {
            echo "   ❌ Controller not found\n";
        }
    }

    echo "\n=== DIAGNOSIS DAN SOLUSI ===\n";
    echo "Berdasarkan hasil check di atas:\n\n";

    echo "1. 🔍 CEK HASIL 'Permission yang ditemukan':\n";
    echo "   - Jika kosong: Permission belum tersimpan di database\n";
    echo "   - Jika ada: Lanjut ke step 2\n\n";

    echo "2. 🔍 CEK HASIL 'Test User->can()':\n";
    echo "   - Jika ❌: Ada masalah di User model atau permission format\n";
    echo "   - Jika ✅: Lanjut ke step 3\n\n";

    echo "3. 🔍 CEK HASIL 'Route middleware':\n";
    echo "   - Jika 'Requires permission' tidak sesuai dengan yang ada: Middleware salah\n";
    echo "   - Jika 'Access: DENIED': Permission tidak match\n\n";

    echo "4. 🔍 CEK HASIL 'Controller authorization':\n";
    echo "   - Jika ada authorize(): Mungkin ada double check\n";
    echo "   - Jika tidak ada: Normal untuk resource routes\n\n";

    echo "💡 KEMUNGKINAN MASALAH:\n";
    echo "A. Permission UI menyimpan format berbeda (misal: master-coa[view] vs master-coa-view)\n";
    echo "B. Middleware route menggunakan permission name yang berbeda\n";
    echo "C. Controller ada authorize() tambahan yang conflict\n";
    echo "D. Session/cache permission belum refresh\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
