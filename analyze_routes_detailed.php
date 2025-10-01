<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "🔍 ANALISIS MENDETAIL ROUTE SISTEM AYP SIS\n";
echo "============================================\n\n";

// Get all routes
$routes = collect(Route::getRoutes());

// Analyze by category
$categories = [
    'master' => $routes->filter(fn($route) => str_starts_with($route->getName() ?? '', 'master.')),
    'auth' => $routes->filter(fn($route) => in_array($route->getName(), ['login', 'logout', 'register.karyawan', 'register.user'])),
    'approval' => $routes->filter(fn($route) => str_starts_with($route->getName() ?? '', 'approval')),
    'pranota' => $routes->filter(fn($route) => str_contains($route->getName() ?? '', 'pranota')),
    'pembayaran' => $routes->filter(fn($route) => str_contains($route->getName() ?? '', 'pembayaran')),
    'tagihan' => $routes->filter(fn($route) => str_contains($route->getName() ?? '', 'tagihan')),
    'perbaikan' => $routes->filter(fn($route) => str_contains($route->getName() ?? '', 'perbaikan')),
    'admin' => $routes->filter(fn($route) => str_starts_with($route->getName() ?? '', 'admin.')),
    'profile' => $routes->filter(fn($route) => str_starts_with($route->getName() ?? '', 'profile.')),
];

echo "📊 DISTRIBUSI ROUTE BERDASARKAN KATEGORI:\n";
echo "==========================================\n";

$totalRoutes = $routes->count();
foreach ($categories as $category => $categoryRoutes) {
    $count = $categoryRoutes->count();
    $percentage = round(($count / $totalRoutes) * 100, 1);
    echo sprintf("%-15s: %3d routes (%s%%)\n", strtoupper($category), $count, $percentage);
}

$uncategorized = $totalRoutes - array_sum(array_map(fn($cat) => $cat->count(), $categories));
$uncategorizedPercentage = round(($uncategorized / $totalRoutes) * 100, 1);
echo sprintf("%-15s: %3d routes (%s%%)\n", "LAINNYA", $uncategorized, $uncategorizedPercentage);
echo sprintf("%-15s: %3d routes\n", "TOTAL", $totalRoutes);

echo "\n🔍 ANALISIS PER KATEGORI:\n";
echo "==========================\n\n";

// Analyze each category in detail
foreach ($categories as $categoryName => $categoryRoutes) {
    if ($categoryRoutes->count() === 0) continue;

    echo "📁 " . strtoupper($categoryName) . " (" . $categoryRoutes->count() . " routes)\n";
    echo str_repeat("-", 50) . "\n";

    // Group by controller
    $byController = $categoryRoutes->groupBy(function($route) {
        $action = $route->getAction('controller') ?? '';
        if (str_contains($action, '@')) {
            return explode('@', $action)[0];
        }
        return 'Closure';
    });

    foreach ($byController as $controller => $routes) {
        $controllerName = class_basename($controller);
        echo "  🎯 $controllerName: {$routes->count()} routes\n";

        // Show methods for this controller
        $methods = $routes->groupBy(function($route) {
            $action = $route->getAction('controller') ?? '';
            if (str_contains($action, '@')) {
                return explode('@', $action)[1];
            }
            return 'closure';
        });

        foreach ($methods as $method => $methodRoutes) {
            if ($methodRoutes->count() > 1) {
                echo "    ⚠️  $method: {$methodRoutes->count()} routes (POSSIBLE DUPLICATE)\n";
                foreach ($methodRoutes as $route) {
                    echo "      - {$route->getName()} ({$route->uri()})\n";
                }
            }
        }
    }
    echo "\n";
}

echo "🚨 POTENSI MASALAH YANG DITEMUKAN:\n";
echo "===================================\n\n";

// Find potential issues
$issues = [];

// 1. Find routes with same URI but different names
$routesByUri = $routes->groupBy('uri');
foreach ($routesByUri as $uri => $sameUriRoutes) {
    if ($sameUriRoutes->count() > 1) {
        $names = $sameUriRoutes->pluck('name')->filter()->unique();
        if ($names->count() > 1) {
            $issues[] = "URI '$uri' has multiple route names: " . $names->implode(', ');
        }
    }
}

// 2. Find routes with very similar names
$routeNames = $routes->pluck('name')->filter();
$similarRoutes = [];
foreach ($routeNames as $name1) {
    foreach ($routeNames as $name2) {
        if ($name1 !== $name2 && similar_text($name1, $name2, $percent) && $percent > 85) {
            $key = implode('|', sorted([$name1, $name2]));
            if (!isset($similarRoutes[$key])) {
                $similarRoutes[$key] = [$name1, $name2, $percent];
            }
        }
    }
}

// 3. Find routes that might be unused (no controller method)
$controllerIssues = [];
$routes->each(function($route) use (&$controllerIssues) {
    $action = $route->getAction('controller');
    if ($action && str_contains($action, '@')) {
        [$controller, $method] = explode('@', $action);
        if (!method_exists($controller, $method)) {
            $controllerIssues[] = "Route '{$route->getName()}' -> {$controller}@{$method} (method not found)";
        }
    }
});

// Output issues
if (!empty($issues)) {
    echo "1️⃣ ROUTE DENGAN URI SAMA TAPI NAMA BEDA:\n";
    foreach ($issues as $issue) {
        echo "   ❌ $issue\n";
    }
    echo "\n";
}

if (!empty($similarRoutes)) {
    echo "2️⃣ ROUTE DENGAN NAMA MIRIP (KEMUNGKINAN DUPLIKAT):\n";
    foreach ($similarRoutes as $similar) {
        echo "   ⚠️  '{$similar[0]}' vs '{$similar[1]}' (similarity: {$similar[2]}%)\n";
    }
    echo "\n";
}

if (!empty($controllerIssues)) {
    echo "3️⃣ ROUTE DENGAN METHOD CONTROLLER TIDAK DITEMUKAN:\n";
    foreach (array_slice($controllerIssues, 0, 10) as $issue) {
        echo "   💥 $issue\n";
    }
    if (count($controllerIssues) > 10) {
        echo "   ... and " . (count($controllerIssues) - 10) . " more issues\n";
    }
    echo "\n";
}

echo "💡 REKOMENDASI OPTIMISASI:\n";
echo "==========================\n\n";

// Calculate optimization potential
$masterRoutes = $categories['master']->count();
$pranotaRoutes = $categories['pranota']->count();
$pembayaranRoutes = $categories['pembayaran']->count();

echo "1️⃣ KONSOLIDASI MASTER DATA:\n";
echo "   - $masterRoutes routes untuk master data\n";
echo "   - Bisa dikonsolidasikan menjadi resource controllers\n";
echo "   - Estimasi pengurangan: " . round($masterRoutes * 0.3) . " routes\n\n";

echo "2️⃣ KONSOLIDASI PRANOTA & PEMBAYARAN:\n";
echo "   - $pranotaRoutes pranota routes + $pembayaranRoutes pembayaran routes\n";
echo "   - Banyak fungsi serupa yang bisa digabungkan\n";
echo "   - Estimasi pengurangan: " . round(($pranotaRoutes + $pembayaranRoutes) * 0.25) . " routes\n\n";

echo "3️⃣ PENGGUNAAN RESOURCE CONTROLLERS:\n";
echo "   - Standardisasi CRUD operations\n";
echo "   - Reduce code duplication\n";
echo "   - Improve maintainability\n\n";

$potentialReduction = round($masterRoutes * 0.3) + round(($pranotaRoutes + $pembayaranRoutes) * 0.25);
$finalRouteCount = $totalRoutes - $potentialReduction;

echo "📈 PROYEKSI OPTIMISASI:\n";
echo "   Route saat ini: $totalRoutes\n";
echo "   Potensi pengurangan: $potentialReduction\n";
echo "   Route setelah optimisasi: $finalRouteCount\n";
echo "   Pengurangan: " . round(($potentialReduction / $totalRoutes) * 100, 1) . "%\n\n";

function sorted($array) {
    sort($array);
    return $array;
}
