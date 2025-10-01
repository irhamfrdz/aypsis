<?php
require_once 'vendor/autoload.php';

// Setup Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CEK DUPLIKASI MIDDLEWARE DAN GLOBAL MIDDLEWARE ===\n\n";

try {
    $router = app('router');

    // Routes yang ingin dicek
    $routesToCheck = [
        'master.cabang.index',
        'master-coa-index',
        'master.tipe-akun.index',
        'master.kode-nomor.index',
        'master.nomor-terakhir.index',
        'tujuan.index',
        'kegiatan.index'
    ];

    foreach ($routesToCheck as $routeName) {
        echo "🛣️  ROUTE: $routeName\n";

        try {
            $route = $router->getRoutes()->getByName($routeName);
            if ($route) {
                $middleware = $route->middleware();

                echo "   📍 URI: " . $route->uri() . "\n";
                echo "   🛡️  Total middleware: " . count($middleware) . "\n";

                // Tampilkan semua middleware
                foreach ($middleware as $index => $mw) {
                    echo "   [$index] $mw\n";
                }

                // Cek apakah ada duplikasi middleware 'can:'
                $canMiddlewares = array_filter($middleware, function($m) {
                    return strpos($m, 'can:') === 0;
                });

                echo "   🔑 Permission middleware: " . count($canMiddlewares) . "\n";
                foreach ($canMiddlewares as $canMW) {
                    $perm = str_replace('can:', '', $canMW);
                    echo "      - $perm\n";
                }

                // Cek apakah ada duplikasi
                $uniqueCanMiddlewares = array_unique($canMiddlewares);
                if (count($canMiddlewares) > count($uniqueCanMiddlewares)) {
                    echo "   ⚠️  DUPLIKASI DITEMUKAN!\n";
                    $duplicates = array_diff_assoc($canMiddlewares, $uniqueCanMiddlewares);
                    foreach ($duplicates as $dup) {
                        echo "      🔄 Duplicate: $dup\n";
                    }
                }

                echo "\n";

            } else {
                echo "   ❌ Route tidak ditemukan\n\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n\n";
        }
    }

    echo "=== CEK MIDDLEWARE GROUP ===\n";

    // Cek apakah ada middleware group yang menambahkan can: middleware
    $middlewareGroups = config('app.middleware_groups', []);
    if (isset($middlewareGroups['web'])) {
        echo "Web middleware group:\n";
        foreach ($middlewareGroups['web'] as $mw) {
            echo "  - $mw\n";
        }
    }

    echo "\n=== CEK ROUTE MODEL BINDING ===\n";

    // Cek apakah ada route model binding yang menambahkan middleware
    $routes = $router->getRoutes();
    foreach ($routes as $route) {
        if (in_array($route->getName(), $routesToCheck)) {
            echo "Route: " . $route->getName() . "\n";
            echo "  Action: " . $route->getActionName() . "\n";

            // Cek route parameters
            $params = $route->parameterNames();
            if (!empty($params)) {
                echo "  Parameters: " . implode(', ', $params) . "\n";
            }
            echo "\n";
        }
    }

    echo "=== ANALISA MASALAH ===\n";

    echo "Jika ada route yang menunjukkan multiple 'can:' middleware dengan permission yang sama:\n";
    echo "1. Kemungkinan ada duplikasi definisi route di routes/web.php\n";
    echo "2. Kemungkinan ada middleware group yang menambahkan middleware\n";
    echo "3. Kemungkinan ada RouteServiceProvider yang menambahkan middleware global\n\n";

    echo "SOLUSI:\n";
    echo "A. Hapus duplikasi route definition\n";
    echo "B. Pastikan hanya ada 1 middleware 'can:' per action\n";
    echo "C. Untuk action 'index' hanya butuh 'can:module-view'\n";
    echo "D. Untuk action 'create' baru butuh 'can:module-create'\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
